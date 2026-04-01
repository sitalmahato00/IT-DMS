# IT-DMS Laragon Setup Guide

## Prerequisites
- **Laragon** installed from [https://laragon.org/](https://laragon.org/)
- **Git** for cloning/managing the repository
- Basic command-line knowledge

---

## 📋 Step-by-Step Setup

### 1. **Prepare Project Location**

Option A: If you haven't moved the project yet:
```powershell
# Move project to Laragon's www directory
Move-Item "d:\DIT MMP\5th sem\minor project\IT-DMS" "C:\laragon\www\IT-DMS"
cd C:\laragon\www\IT-DMS
```

Option B: If your project is already in Laragon:
```powershell
cd C:\laragon\www\IT-DMS
```

### 2. **Start Laragon Services**
- Open Laragon application
- Click **"Start All"** to start Apache and MySQL
- Verify MySQL is running (check icon in Laragon window)

### 3. **Configure Database**

```powershell
# Access MySQL command line
# Option 1: Use Laragon's built-in terminal
# Option 2: Or use command line
mysql -u root -p

# Create database
CREATE DATABASE dit;
EXIT;
```

**Default Laragon MySQL credentials:**
- Username: `root`
- Password: (empty by default)
- Host: `127.0.0.1`
- Port: `3306`

### 4. **Setup .env File**

Copy and modify the example .env file:

```powershell
# Copy from example
Copy-Item .env.example .env
```

Edit `.env` file with these Laragon settings:

```env
APP_NAME=IT-DMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://it-dms.test

# Database Configuration for Laragon
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dit
DB_USERNAME=root
DB_PASSWORD=

# Session Driver
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache and Queue
CACHE_STORE=database
QUEUE_CONNECTION=database

# Redis (optional - if you want to use Redis)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Mail Configuration
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### 5. **Install PHP Dependencies**

```powershell
# Install composer packages
composer install
```

### 6. **Generate Application Key**

```powershell
# Generate Laravel app key
php artisan key:generate
```

### 7. **Setup Database**

```powershell
# Run migrations
php artisan migrate

# (Optional) Seed the database with sample data
php artisan db:seed
```

### 8. **Create Storage Symlink**

```powershell
# Create symbolic link for storage
php artisan storage:link
```

### 9. **Install Frontend Dependencies**

```powershell
# Install npm packages
npm install

# Build frontend assets
npm run build
```

### 10. **Configure Virtual Host (Optional but Recommended)**

To access your project via `http://it-dms.test` instead of `http://localhost:8000`:

1. **Add to Windows hosts file** (`C:\Windows\System32\drivers\etc\hosts`):
   ```
   127.0.0.1 it-dms.test
   ```

2. **Configure Laragon virtual host**:
   - Open Laragon
   - Click **"Menu"** → **"Apache"** → **"httpd-vhosts.conf"**
   - Add this configuration:
   ```apache
   <VirtualHost *:80>
       ServerName it-dms.test
       DocumentRoot C:\laragon\www\IT-DMS\public
       <Directory C:\laragon\www\IT-DMS\public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. **Restart Apache**:
   - In Laragon: Click **"Stop All"** then **"Start All"**

### ⚙️ Switching Between Docker and Laragon

If you want to keep both setups available:

**.env for Docker:**
```env
DB_HOST=mysql
REDIS_HOST=redis
```

**.env for Laragon:**
```env
DB_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
```

---

## 🚀 Daily Usage

### Starting Development
```powershell
# 1. Open Laragon and click "Start All"
# 2. Navigate to project
cd C:\laragon\www\IT-DMS

# 3. (Optional) Start frontend watcher for changes
npm run dev

# 4. Access application
# Via Virtual Host: http://it-dms.test
# Via localhost: http://localhost
```

### Accessing Database
```powershell
# Option 1: Use Laragon's MySQL tool (Menu → MySQL)
# Option 2: Command line
mysql -u root dit

# Option 3: Use phpMyAdmin (included in Laragon)
# http://localhost/phpmyadmin
```

### Viewing Logs
```powershell
# Application logs
tail -f storage/logs/laravel.log

# Or open in editor
code storage/logs/laravel.log
```

---

## ❌ Troubleshooting

### Port 80 Already in Use
```powershell
# Check what's using port 80
netstat -ano | findstr :80

# If Apache won't start, try a different port:
# Edit Laragon → Apache → httpd.conf
# Change: Listen 80 to Listen 8080
```

### Database Connection Error
```powershell
# Verify MySQL is running in Laragon
# Check credentials match .env file
# Test connection
mysql -u root -p< nul

# If this fails, restart MySQL from Laragon menu
```

### Permission Denied on storage/
```powershell
# Fix storage permissions
Attrib -R -S storage /S /D
```

### PHP Version Issues
```powershell
# Check PHP version
php --version

# If wrong version, change in Laragon:
# Menu → PHP → Select Version
# Then restart Laragon
```

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laragon Documentation](https://laragon.org/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

## 🔄 Quick Comparison: Docker vs Laragon

| Feature | Docker | Laragon |
|---------|--------|---------|
| Setup Time | ~10 mins | ~5 mins |
| Performance | Medium | Better (Direct System) |
| Database Management | Container-based | Direct MySQL |
| Consistency | Across machines | Local only |
| Memory Usage | Higher | Lower |
| Best for | Team/Production | Solo Development |

---

**Last Updated:** April 2026
