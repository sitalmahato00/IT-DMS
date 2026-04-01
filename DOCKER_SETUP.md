# Docker Setup Guide for Laravel IT-DMS Project

## Prerequisites
- Docker Desktop installed and running
- Docker Compose installed

## 🚀 Complete Startup Guide

Follow these steps in order to run the project:

### Step 1: Open Terminal and Navigate to Project
```powershell
cd "d:\DIT MMP\5th sem\minor project\IT-DMS"
```

### Step 2: Start Docker Containers
```powershell
docker-compose up -d
```
**What this does**: Starts all containers (PHP app, Nginx, MySQL) in detached mode (background)

### Step 3: Verify Containers Are Running
```powershell
docker-compose ps
```
**Expected output**: All containers should show "Up" status with green indicators

### Step 4: Set Up Laravel Environment
```powershell
# Copy environment file
docker-compose exec app bash -c "cp .env.example .env"

# Generate application key (encryption key for Laravel)
docker-compose exec app php artisan key:generate
```

### Step 5: Install PHP Dependencies
```powershell
# Install Composer packages
docker-compose exec app composer install
```

### Step 6: Set Up Database
```powershell
# Run migrations to create database tables
docker-compose exec app php artisan migrate

# Optional: Seed database with sample data
docker-compose exec app php artisan db:seed

# Create storage link for file uploads
docker-compose exec app php artisan storage:link
```

### Step 7: Install Frontend Dependencies
```powershell
# Install npm packages
docker-compose exec app npm install
```

### Step 8: Start Development Servers

**Option A: Run with Vite Development Server** (for active development)
```powershell
# Start the full Docker development stack
docker-compose up -d

# If the frontend dev server ever stops, restart only Vite
docker-compose up -d vite
```

**Option B: Build Frontend Assets** (for production)
```powershell
# Build frontend assets
docker-compose exec app npm run build
```

### Step 9: Access Your Application

Open these URLs in your browser:

| Service | URL | Purpose |
|---------|-----|---------|
| **Laravel App** | http://localhost | Main application |
| **Vite Dev Server** | http://localhost:5173 | Frontend dev (when the `vite` service is running) |
| **MySQL** | localhost:3306 | Database server |

---

## Quick Start

### 1. Build and Start Containers
```powershell
cd "path\to\IT-DMS"
docker-compose up -d --build
```

### 2. Run Laravel Setup Commands
```powershell
# Generate environment file
docker-compose exec app bash -c "cp .env.example .env"

# Generate application key
docker-compose exec app php artisan key:generate

# Install composer dependencies
docker-compose exec app composer install

# Run database migrations
docker-compose exec app php artisan migrate

# Create storage link for file uploads
docker-compose exec app php artisan storage:link

# Optional: Seed database (if seeders exist)
docker-compose exec app php artisan db:seed
```

### 3. Frontend Development
```powershell
# Install npm dependencies
docker-compose exec app npm install

# Start or restart the Vite dev server
docker-compose up -d vite

# Or build for production
docker-compose exec app npm run build
```

### 4. Access the Application
- **Application**: http://localhost
- **MySQL**: localhost:3306
- **Vite Dev Server**: http://localhost:5173 (when the `vite` service is running)

## Database Credentials
- **Database**: dit
- **Username**: laravel
- **Password**: laravel_password
- **Root Password**: root_password

## Common Commands

### View Logs
```bash
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f nginx
```

### Access Application Shell
```bash
docker-compose exec app bash
```

### Run Frontend Build Commands
```powershell
# Install npm packages
docker-compose exec app npm install

# Start or restart the Vite development server
docker-compose up -d vite

# Build for production
docker-compose exec app npm run build

# Check npm version
docker-compose exec app npm --version

# Check Node version
docker-compose exec app node --version
```

### Database Access (MySQL CLI)
```powershell
docker-compose exec mysql mysql -u laravel -p dit
# Password: laravel_password
```

### Run Database Migrations
```powershell
docker-compose exec app php artisan migrate
```

### Seed Database
```powershell
docker-compose exec app php artisan db:seed
```

### Stop Containers
```bash
docker-compose stop
```

### Stop and Remove All Containers
```bash
docker-compose down
```

### Remove All Data (including database)
```bash
docker-compose down -v
```

### Rebuild Images
```powershell
docker-compose down
docker-compose up -d --build
```

### View Container Logs
```powershell
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f mysql
docker-compose logs -f nginx
```

## Stop and Restart Project

### Stop All Containers (Keep Data)
```powershell
docker-compose stop
```
All data is preserved and containers can be restarted.

### Start All Containers Again
```powershell
docker-compose start
```

### Stop and Remove All Containers (Keep Data)
```powershell
docker-compose down
```
Containers are removed but database data persists in the `mysql_data` volume.

### Remove Everything Including Database Data
```powershell
docker-compose down -v
```
**Warning**: This deletes all database data. Only use if you want a fresh start.

### Completely Restart Project
```powershell
# Stop everything and remove containers
docker-compose down

# Start everything again
docker-compose up -d

# Verify containers are running
docker-compose ps
```

## Environment Variables

Database configuration in `docker-compose.yml`:
- `DB_DATABASE`: dit
- `DB_USERNAME`: laravel
- `DB_PASSWORD`: laravel_password
- `DB_HOST`: mysql
- `DB_PORT`: 3306

The `.env` file is automatically configured during setup:
```powershell
cp .env.example .env
docker-compose exec app php artisan key:generate
```

## Troubleshooting

### Service Status
Check if all containers are running:
```powershell
docker-compose ps
```

### Restart Services
```powershell
docker-compose restart
```

### Port Already in Use
If port 80 or 3306 is in use, modify the ports in `docker-compose.yml`:
```yaml
ports:
  - "8080:80"    # Change app port
  - "3307:3306"  # Change MySQL port  
```

### Database Connection Issues
Ensure MySQL is running and initialized:
```powershell
docker-compose restart mysql
Start-Sleep -Seconds 5
docker-compose exec app php artisan migrate
```

### Permission Issues on Storage
```powershell
docker-compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

### Clear Application Cache
```powershell
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

## Services Included

### 1. PHP-FPM (Laravel App) - `laravel-app`
- PHP 8.2 with Debian Bullseye base
- Runs on port 9000 (internal)
- Handles all application logic

### 2. Nginx Web Server - `laravel-nginx`
- Alpine Linux base
- Listens on port 80 (http://localhost)
- Forwards requests to PHP-FPM

### 3. MySQL Database - `laravel-mysql`
- MySQL 5.7
- Runs on port 3306
- Database name: `dit`
- User: `laravel` / Password: `laravel_password`
- Data persisted in `mysql_data` volume

## File Structure
```
IT-DMS/
├── Dockerfile              # PHP-FPM container configuration
├── docker-compose.yml      # Docker services orchestration
├── DOCKER_SETUP.md         # This file
├── .env.example            # Environment template
└── docker/
    └── nginx/
        └── conf.d/         # Nginx configuration
```

## Important Notes
- Application files are mounted as volumes for live development
- Database data persists in `mysql_data` volume
- Nginx is the reverse proxy for all HTTP requests
- All containers use the `laravel-network` for internal communication
- Redis has been disabled due to platform compatibility issues (can be re-enabled if needed)

---

## ⚡ Quick Reference Cheat Sheet

### Initial Setup (First Time)
```powershell
# 1. Navigate to project
cd "d:\DIT MMP\5th sem\minor project\IT-DMS"

# 2. Start containers
docker-compose up -d

# 3. Setup Laravel
docker-compose exec app bash -c "cp .env.example .env"
docker-compose exec app php artisan key:generate
docker-compose exec app composer install

# 4. Setup Database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link

# 5. Setup Frontend
docker-compose exec app npm install

# 6. For development: Start Vite
docker-compose up -d vite
```

### Daily Development
```powershell
# Start all containers (includes Vite)
docker-compose up -d

# Restart only Vite if needed
docker-compose up -d vite

# View logs (troubleshooting)
docker-compose logs -f

# Stop when done
docker-compose stop
```

### Useful Commands
```powershell
# Check container status
docker-compose ps

# Run migrations
docker-compose exec app php artisan migrate

# Build frontend
docker-compose exec app npm run build

# Clear cache
docker-compose exec app php artisan cache:clear

# Open app shell
docker-compose exec app bash

# View MySQL
docker-compose exec mysql mysql -u laravel -p dit
```

### Reset Database Only
```powershell
docker-compose exec app php artisan migrate:refresh
docker-compose exec app php artisan db:seed
```

### Complete Fresh Start
```powershell
docker-compose down -v
docker-compose up -d
# Then run Initial Setup steps again
```
