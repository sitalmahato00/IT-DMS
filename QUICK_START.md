# IT-DMS Quick Start Guide

## � Choose Your Development Environment

### Option 1: Laragon (Recommended for Windows Solo Development)
**Faster setup, better performance, direct system integration**

```powershell
# Move to Laragon directory and run setup
cd C:\laragon\www\IT-DMS
.\setup-laragon.ps1
```

Then open: **http://it-dms.test** or **http://localhost**

📖 **Detailed guide:** See [LARAGON_SETUP.md](LARAGON_SETUP.md)

---

### Option 2: Docker (Recommended for Team/Production)
**Consistent across machines, production-like environment**

See instructions below ⬇️

---

## 🚀 Initial Setup (First Time Only) - Docker

```powershell
# 1. Navigate to project
cd "d:\DIT MMP\5th sem\minor project\IT-DMS"

# 2. Start containers
docker-compose up -d

# 3. Copy environment file
docker-compose exec app bash -c "cp .env.example .env"

# 4. Generate app key
docker-compose exec app php artisan key:generate

# 5. Install PHP dependencies
docker-compose exec app composer install

# 6. Run migrations
docker-compose exec app php artisan migrate

# 7. Create storage link
docker-compose exec app php artisan storage:link

# 8. Install npm dependencies
docker-compose exec app npm install

# 9. Build frontend assets
docker-compose exec app npm run build

# 10. (Optional) Seed database
docker-compose exec app php artisan db:seed
```

---

## 📱 After Computer Restart (3 Steps)

```powershell
# 1. Navigate to project
cd "d:\DIT MMP\5th sem\minor project\IT-DMS"

# 2. Start containers
docker-compose up -d

# 3. Verify containers (should show all "Up")
docker-compose ps
```

Then open: **http://localhost**

---

## ⚡ Quick One-Line Commands

### Start All Services
```powershell
docker-compose up -d
```

This starts PHP, Nginx, MySQL, and the Vite dev server.
The Vite container clears any stale `public/hot` file before it starts, so the frontend stays pointed at a live dev server instead of a dead hot-file URL.

### Check Status
```powershell
docker-compose ps
```

### Stop All Services
```powershell
docker-compose stop
```

### Restart All Services
```powershell
docker-compose restart
```

### View Logs
```powershell
docker-compose logs -f
```

### Complete Reset (Loses Data!)
```powershell
docker-compose down -v && docker-compose up -d
```

---

## 🎨 Frontend Development (Vite)

### Install npm packages
```powershell
docker-compose exec app npm install
```

### Start or Restart Vite Dev Server (HMR Enabled)
```powershell
docker-compose up -d vite
```
Access at: **http://localhost:5173**

`docker-compose up -d` already starts the Vite service. Run the command above when you only need to bring the frontend dev server back up.

### Build for Production
```powershell
docker-compose exec app npm run build
```

### Install specific packages
```powershell
docker-compose exec app npm install package-name
```

---

## 🛠️ Laravel Artisan Commands

### Database & Migrations
```powershell
# Run pending migrations
docker-compose exec app php artisan migrate

# Rollback last migration
docker-compose exec app php artisan migrate:rollback

# Rollback all & re-run
docker-compose exec app php artisan migrate:refresh

# Refresh & seed with data
docker-compose exec app php artisan migrate:refresh --seed

# Create migration file
docker-compose exec app php artisan make:migration create_table_name

# Seed database
docker-compose exec app php artisan db:seed

# Seed specific seeder
docker-compose exec app php artisan db:seed --class=SeederName
```

### Cache & Configuration
```powershell
# Clear all caches
docker-compose exec app php artisan cache:clear

# Clear config cache
docker-compose exec app php artisan config:clear

# Clear route cache
docker-compose exec app php artisan route:clear

# Clear view cache
docker-compose exec app php artisan view:clear

# Clear all application caches
docker-compose exec app php artisan optimize:clear
```

### Models & Resources
```powershell
# Create model
docker-compose exec app php artisan make:model ModelName

# Create model with migration
docker-compose exec app php artisan make:model ModelName -m

# Create model with migration & factory & seeder
docker-compose exec app php artisan make:model ModelName -mfs

# Create controller
docker-compose exec app php artisan make:controller ControllerName

# Create resource controller (CRUD)
docker-compose exec app php artisan make:controller ControllerName --resource

# Create migration
docker-compose exec app php artisan make:migration migration_name

# Create seeder
docker-compose exec app php artisan make:seeder SeederName

# Create factory
docker-compose exec app php artisan make:factory FactoryName
```

### Routes & Views
```powershell
# List all routes
docker-compose exec app php artisan route:list

# Create view
docker-compose exec app php artisan make:view view_name

# Cache routes (production)
docker-compose exec app php artisan route:cache

# Clear route cache
docker-compose exec app php artisan route:clear
```

### Storage & Symlinks
```powershell
# Create storage symlink
docker-compose exec app php artisan storage:link

# Clear symlinks
docker-compose exec app php artisan storage:unlink
```

### Development & Debugging
```powershell
# Start Tinker REPL
docker-compose exec app php artisan tinker

# Clear key
docker-compose exec app php artisan key:clear

# Generate new key
docker-compose exec app php artisan key:generate

# List all commands
docker-compose exec app php artisan list

# Get command help
docker-compose exec app php artisan help command_name
```

### Authentication
```powershell
# Scaffold Laravel Breeze (auth)
docker-compose exec app php artisan breeze:install

# Publish assets
docker-compose exec app php artisan vendor:publish
```

---

## 🗄️ MySQL Database Access

### Access MySQL CLI
```powershell
docker-compose exec mysql mysql -u laravel -p dit
# Password: laravel_password
```

### Common MySQL Commands
```sql
-- Show all databases
SHOW DATABASES;

-- Use database
USE dit;

-- Show all tables
SHOW TABLES;

-- Show table structure
DESCRIBE table_name;

-- Show all data in table
SELECT * FROM table_name;

-- Exit MySQL
EXIT;
```

---

## 🔧 Troubleshooting Commands

### Check Container Logs
```powershell
# All logs
docker-compose logs

# Specific service
docker-compose logs app
docker-compose logs mysql
docker-compose logs nginx

# Follow logs (live)
docker-compose logs -f
```

### Access App Shell
```powershell
docker-compose exec app bash
```

### Fix Permissions
```powershell
docker-compose exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache
```

### Rebuild Docker Image
```powershell
docker-compose down
docker-compose up -d --build
```

### Remove Everything (Nuclear Option)
```powershell
docker-compose down -v
docker-compose up -d
```

---

## 📋 Environment File (.env)

Key variables in `.env`:
```
APP_NAME=IT-DMS
APP_ENV=local
APP_KEY=[auto-generated by artisan key:generate]
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=dit
DB_USERNAME=laravel
DB_PASSWORD=laravel_password

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

---

## 🌐 Access Points

| Service | URL | Purpose |
|---------|-----|---------|
| Laravel App | http://localhost | Main application |
| Vite Dev | http://localhost:5173 | Frontend dev (when running `npm run dev`) |
| MySQL | localhost:3306 | Database |
| phpMyAdmin | N/A | Not included |

---

## 💾 Database Credentials

- **Host**: localhost:3306
- **Database**: dit
- **Username**: laravel
- **Password**: laravel_password
- **Root Password**: root_password

---

## ⚙️ Docker Services

| Service | Container | Image | Port |
|---------|-----------|-------|------|
| Laravel App | laravel-app | laravel-app-npm | 9000 |
| Nginx Web | laravel-nginx | nginx:1.21-alpine | 80, 443 |
| MySQL DB | laravel-mysql | mysql:5.7 | 3306 |

---

## 📊 Useful Verification Commands

```powershell
# Check Docker is running
docker --version

# Check all containers
docker ps -a

# Check Docker images
docker images

# Check npm version
docker-compose exec app npm --version

# Check Node version
docker-compose exec app node --version

# Check PHP version
docker-compose exec app php --version

# Check Composer version
docker-compose exec app composer --version

# Check Laravel version
docker-compose exec app php artisan --version
```

---

## 🔄 Workflow During Development

```powershell
# 1. Start containers (includes Vite)
docker-compose up -d

# 2. Access app at http://localhost
# 3. Make changes to code (auto-reloads with HMR)
# 4. If only Vite stops, restart it
docker-compose up -d vite

# 5. Stop containers when done
docker-compose stop
```

---

## 🎯 Quick Reference by Task

### I want to...

**Run the project**
```powershell
cd "d:\DIT MMP\5th sem\minor project\IT-DMS" && docker-compose up -d
```

**Stop the project**
```powershell
docker-compose stop
```

**Reset database**
```powershell
docker-compose exec app php artisan migrate:refresh --seed
```

**Create new migration**
```powershell
docker-compose exec app php artisan make:migration create_table_name
```

**Create new controller**
```powershell
docker-compose exec app php artisan make:controller ControllerName --resource
```

**Clear all caches**
```powershell
docker-compose exec app php artisan optimize:clear
```

**Build frontend**
```powershell
docker-compose exec app npm run build
```

**See what's running**
```powershell
docker-compose ps
```

**Access database**
```powershell
docker-compose exec mysql mysql -u laravel -p dit
```

**See live logs**
```powershell
docker-compose logs -f
```

---

## ✅ All Set!

You now have all commands needed to run and develop the IT-DMS project. Bookmark this page! 🚀
