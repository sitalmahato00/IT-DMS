# IT-DMS Laragon Setup Script
# Run this script to quickly set up the project for Laragon development

param(
    [switch]$SkipDatabaseCreation = $false,
    [switch]$SkipMigrations = $false
)

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  IT-DMS Laragon Setup Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if running from project directory
if (-not (Test-Path "artisan")) {
    Write-Host "ERROR: This script must be run from the project root directory!" -ForegroundColor Red
    exit 1
}

Write-Host "[1/9] Checking prerequisites..." -ForegroundColor Yellow

# Check PHP
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: PHP not found. Make sure Laragon PHP is in your PATH!" -ForegroundColor Red
    exit 1
}
Write-Host "✓ PHP found: $(php --version | Select-Object -First 1)" -ForegroundColor Green

# Check Composer
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: Composer not found!" -ForegroundColor Red
    exit 1
}
Write-Host "✓ Composer found" -ForegroundColor Green

# Check MySQL
if (-not (Get-Command mysql -ErrorAction SilentlyContinue)) {
    Write-Host "WARNING: MySQL not found. Database operations may fail." -ForegroundColor Yellow
}
else {
    Write-Host "✓ MySQL found" -ForegroundColor Green
}

Write-Host ""
Write-Host "[2/9] Copying .env file..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "✓ .env already exists" -ForegroundColor Green
}
else {
    if (Test-Path ".env.laragon") {
        Copy-Item ".env.laragon" ".env"
        Write-Host "✓ Copied from .env.laragon" -ForegroundColor Green
    }
    else {
        Copy-Item ".env.example" ".env"
        Write-Host "✓ Copied from .env.example" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "[3/9] Generating application key..." -ForegroundColor Yellow
php artisan key:generate
Write-Host "✓ Application key generated" -ForegroundColor Green

Write-Host ""
Write-Host "[4/9] Installing Composer dependencies..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Composer install completed" -ForegroundColor Green
}
else {
    Write-Host "ERROR: Composer install failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[5/9] Creating storage symlink..." -ForegroundColor Yellow
php artisan storage:link
Write-Host "✓ Storage symlink created" -ForegroundColor Green

Write-Host ""
Write-Host "[6/9] Installing npm dependencies..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ npm install completed" -ForegroundColor Green
}
else {
    Write-Host "ERROR: npm install failed!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "[7/9] Building frontend assets..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Frontend assets built" -ForegroundColor Green
}
else {
    Write-Host "ERROR: Frontend build failed!" -ForegroundColor Red
    exit 1
}

# Database setup
if (-not $SkipDatabaseCreation) {
    Write-Host ""
    Write-Host "[8/9] Setting up database..." -ForegroundColor Yellow
    
    try {
        # Check if database exists
        $dbCheck = mysql -u root -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'dit';" 2>$null
        
        if ($null -eq $dbCheck -or $dbCheck -eq "") {
            Write-Host "Creating database 'dit'..." -ForegroundColor Gray
            mysql -u root -e "CREATE DATABASE IF NOT EXISTS dit;" 2>$null
            if ($LASTEXITCODE -eq 0) {
                Write-Host "✓ Database created" -ForegroundColor Green
            }
        }
        else {
            Write-Host "✓ Database 'dit' already exists" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "WARNING: Could not verify/create database. Please create manually if needed." -ForegroundColor Yellow
        Write-Host "Run in MySQL: CREATE DATABASE dit;" -ForegroundColor Yellow
    }
}

# Migrations
if (-not $SkipMigrations) {
    Write-Host ""
    Write-Host "[9/9] Running migrations..." -ForegroundColor Yellow
    php artisan migrate
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Migrations completed" -ForegroundColor Green
    }
    else {
        Write-Host "WARNING: Migrations failed. Check database configuration." -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Setup Complete!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Make sure Laragon is running (Start All)" -ForegroundColor White
Write-Host "2. Access the application:" -ForegroundColor White
Write-Host "   → http://it-dms.test (if virtual host configured)" -ForegroundColor White
Write-Host "   → http://localhost (direct access)" -ForegroundColor White
Write-Host "3. (Optional) Start frontend watcher: npm run dev" -ForegroundColor White
Write-Host ""
Write-Host "For detailed instructions, see: LARAGON_SETUP.md" -ForegroundColor Cyan
Write-Host ""
