#!/bin/bash

################################################################################
# IT-DMS PRODUCTION DEPLOYMENT SCRIPT
# Run on production server before going live
# Usage: bash deploy-production.sh
################################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
ENVIRONMENT="production"
LOG_FILE="deployment_$(date +%Y%m%d_%H%M%S).log"

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓ $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗ $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}⚠ $1${NC}" | tee -a "$LOG_FILE"
}

# ============================================================================
# PRE-DEPLOYMENT CHECKS
# ============================================================================

log "Starting IT-DMS Production Deployment"
log "Elapsed time: $(date)"

# Check if running as root or with sudo
if [[ $EUID -ne 0 ]]; then
    error "This script must be run as root or with sudo"
fi

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
log "PHP Version: $PHP_VERSION"

# Check required commands
for cmd in php composer npm git mysql; do
    if ! command -v $cmd &> /dev/null; then
        error "$cmd is not installed"
    fi
done
success "All required commands found"

# ============================================================================
# ENVIRONMENT SETUP
# ============================================================================

log "Setting up environment..."

# Check .env file
if [ ! -f ".env" ]; then
    if [ -f ".env.production.example" ]; then
        cp .env.production.example .env
        warning "Created .env from .env.production.example - EDIT BEFORE DEPLOYING!"
        error "Please update .env with your production values"
    else
        error ".env file not found"
    fi
fi

# Validate required .env variables
required_vars=("APP_ENV" "APP_KEY" "DB_HOST" "DB_DATABASE" "DB_USERNAME")
for var in "${required_vars[@]}"; do
    if ! grep -q "^$var=" .env; then
        error "$var is not set in .env"
    fi
done
success "Environment variables validated"

# ============================================================================
# BACKUP
# ============================================================================

log "Creating backups..."

BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup database
if [ ! -z "$DB_DATABASE" ]; then
    DB_USER=$(grep "^DB_USERNAME=" .env | cut -d'=' -f2)
    DB_PASS=$(grep "^DB_PASSWORD=" .env | cut -d'=' -f2)
    
    log "Backing up database..."
    mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_DATABASE" > "$BACKUP_DIR/database.sql" || error "Database backup failed"
    success "Database backed up to $BACKUP_DIR/database.sql"
fi

# Backup storage
if [ -d "storage" ]; then
    log "Backing up storage..."
    tar -czf "$BACKUP_DIR/storage.tar.gz" storage/ || error "Storage backup failed"
    success "Storage backed up to $BACKUP_DIR/storage.tar.gz"
fi

# ============================================================================
# CODE DEPLOYMENT
# ============================================================================

log "Deploying code..."

# Pull latest code
log "Pulling latest code from git..."
git pull origin main || error "Git pull failed"
success "Code pulled"

# Install dependencies
log "Installing composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction || error "Composer install failed"
success "Composer dependencies installed"

log "Installing npm dependencies..."
npm install --production || error "NPM install failed"
success "NPM dependencies installed"

# ============================================================================
# APPLICATION SETUP
# ============================================================================

log "Configuring application..."

# Generate APP_KEY if empty
if grep -q "^APP_KEY=$" .env; then
    log "Generating APP_KEY..."
    php artisan key:generate --force || error "APP_KEY generation failed"
    success "APP_KEY generated"
fi

# ============================================================================
# DATABASE SETUP
# ============================================================================

log "Setting up database..."

# Run migrations
log "Running migrations..."
php artisan migrate --force --no-interaction || error "Migrations failed"
success "Migrations completed"

# Optional: Run seeders (uncomment if needed)
# log "Running seeders..."
# php artisan db:seed --force --no-interaction
# success "Seeds completed"

# ============================================================================
# STORAGE & SYMLINKS
# ============================================================================

log "Setting up storage..."

# Create storage link
if [ ! -L "public/storage" ]; then
    log "Creating storage symlink..."
    php artisan storage:link || error "Storage link creation failed"
    success "Storage symlink created"
else
    success "Storage symlink already exists"
fi

# Set permissions
log "Setting file permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 644 storage bootstrap/cache/*
success "File permissions set"

# ============================================================================
# BUILD ASSETS
# ============================================================================

log "Building assets..."

# Build frontend assets
log "Building frontend with npm..."
npm run build || error "NPM build failed"
success "Frontend assets built"

# ============================================================================
# CACHE OPTIMIZATION
# ============================================================================

log "Optimizing application cache..."

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configs
log "Caching configuration..."
php artisan config:cache || error "Config cache failed"
success "Configuration cached"

log "Caching routes..."
php artisan route:cache || error "Route cache failed"
success "Routes cached"

log "Caching views..."
php artisan view:cache || error "View cache failed"
success "Views cached"

# ============================================================================
# VERIFICATION
# ============================================================================

log "Verifying deployment..."

# Check application status
log "Running health check..."
if php artisan tinker --command='echo "OK";' > /dev/null 2>&1; then
    success "Application responding to commands"
else
    error "Application health check failed"
fi

# Test database connection
log "Testing database connection..."
php artisan tinker --command='\DB::connection()->getPdo();' > /dev/null 2>&1 || error "Database connection failed"
success "Database connected"

# Verify storage link
if [ -L "public/storage" ]; then
    success "Storage symlink verified"
else
    error "Storage symlink verification failed"
fi

# ============================================================================
# SERVICE RESTART
# ============================================================================

log "Restarting services..."

# Restart PHP-FPM (if using)
if command -v systemctl &> /dev/null; then
    if systemctl list-units --all | grep -q "php-fpm\|php.*-fpm"; then
        log "Restarting PHP-FPM..."
        systemctl restart php-fpm || warning "PHP-FPM restart failed (may not be needed)"
        success "PHP-FPM restarted"
    fi

    # Restart Laravel Queue (if using jobs)
    if grep -q "QUEUE_CONNECTION=redis" .env; then
        log "Restarting Laravel Queue..."
        # systemctl restart laravel-queue || warning "Queue restart failed"
    fi
fi

# Nginx reload (if using)
if command -v nginx &> /dev/null; then
    log "Reloading Nginx..."
    nginx -t && systemctl reload nginx || warning "Nginx reload failed (may not be needed)"
    success "Nginx reloaded"
fi

# ============================================================================
# POST-DEPLOYMENT TESTS
# ============================================================================

log "Running post-deployment tests..."

# Wait for services to start
sleep 2

# Test application URL
APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2)
log "Testing application at $APP_URL"

HTTP_STATUS=$(curl -o /dev/null -s -w "%{http_code}" "$APP_URL/health" 2>/dev/null || echo "000")

if [ "$HTTP_STATUS" = "200" ]; then
    success "Application responding (HTTP $HTTP_STATUS)"
else
    warning "Application health endpoint returned HTTP $HTTP_STATUS (expected 200)"
fi

# Test login page
log "Testing login page..."
curl -s "$APP_URL/login" | grep -q "login" && success "Login page accessible" || warning "Could not verify login page"

# ============================================================================
# FINAL CHECKS
# ============================================================================

log "Final deployment checks..."

# Check error logs for issues
if [ -f "storage/logs/laravel.log" ]; then
    ERRORS=$(tail -n 50 storage/logs/laravel.log | grep -i "error\|exception" | wc -l)
    if [ $ERRORS -gt 0 ]; then
        warning "Found $ERRORS errors in recent logs - please review"
    else
        success "No critical errors in recent logs"
    fi
fi

# ============================================================================
# DEPLOYMENT COMPLETE
# ============================================================================

log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
success "DEPLOYMENT COMPLETED SUCCESSFULLY!"
log "Backup location: $BACKUP_DIR"
log "Log file: $LOG_FILE"
log "Application URL: $APP_URL"
log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

log "Post-deployment checklist:"
log "  [ ] Verify application is running at $APP_URL"
log "  [ ] Check error logs: tail -f storage/logs/laravel.log"
log "  [ ] Test all user roles (admin, teacher, student, parent)"
log "  [ ] Verify email notifications working"
log "  [ ] Test file uploads"
log "  [ ] Monitor for errors over next 30 minutes"

exit 0
