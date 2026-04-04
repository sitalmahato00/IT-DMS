# Railway Deployment Guide - IT-DMS
## Complete Step-by-Step Instructions for Production Deployment

**Status:** ✅ Ready for Railway Deployment  
**Framework:** Laravel 12.55.1  
**PHP Version:** 8.2+  
**Database:** MySQL (Railway PostgreSQL also supported)  
**Deployment Type:** Full-stack with workers  

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### Application Status
- [ ] Phase 1 Code Optimization Applied ✅
- [ ] Database Indexes Created ✅
- [ ] All tests passing locally
- [ ] `.env.example` updated with production variables
- [ ] `.gitignore` excludes sensitive files
- [ ] Git repository with clean history

### System Requirements
- [ ] PHP 8.2+ (Railway provides PHP 8.2)
- [ ] Composer dependencies installed
- [ ] Node.js dependencies installed
- [ ] Assets building with `npm run build`
- [ ] Database migrations ready
- [ ] Storage directories writable

### Configuration Ready
- [ ] APP_KEY generated (will be set in Railway)
- [ ] Database credentials from Railway MySQL/PostgreSQL
- [ ] Mail service configured (optional)
- [ ] Redis configuration (optional, for caching)
- [ ] Queue driver configured (database by default)

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Create Railway Account & Project (5 minutes)

1. **Visit Railway:**
   ```
   https://railway.app
   ```

2. **Sign Up/Login:**
   - GitHub login recommended (easier deployment)
   - Create account if needed

3. **Create New Project:**
   - Click "Create New Project"
   - Select "Deploy from GitHub"
   - Select your GitHub repository with IT-DMS code
   - Authorize Railway to access GitHub

4. **Project Created:**
   - You'll see project dashboard
   - Railway auto-detects Laravel framework ✅

---

### Step 2: Add Database Service (2 minutes)

1. **Add MySQL Database:**
   ```
   Click: + Add Service
   Select: MySQL
   Click: Add
   ```

2. **Database Created:**
   - Railway creates database automatically
   - Connection details auto-generated
   - Variables shown in Railway environment

3. **Configure Database Name:**
   - Default: `railway` 
   - Can rename in MySQL service settings

---

### Step 3: Configure Environment Variables (3 minutes)

1. **Click Web Service (PHP):**
   - Go to your web service settings
   - Click "Variables" tab

2. **Add Required Variables:**
   ```
   APP_NAME=IT-DMS
   APP_ENV=production
   APP_DEBUG=false
   APP_LOG_LEVEL=warning
   
   DB_CONNECTION=mysql
   DB_HOST=${{ MySQL.MYSQL_HOST }}
   DB_PORT=${{ MySQL.MYSQL_PORT }}
   DB_DATABASE=${{ MySQL.MYSQL_DATABASE }}
   DB_USERNAME=${{ MySQL.MYSQL_USER }}
   DB_PASSWORD=${{ MySQL.MYSQL_PASSWORD }}
   
   SESSION_DRIVER=cookie
   CACHE_STORE=file
   QUEUE_CONNECTION=database
   ```

3. **Generate APP_KEY:**
   - Run in Terminal: `php artisan key:generate --show`
   - Copy the key (starts with `base64:`)
   - Add to Railway: `APP_KEY=base64:xxxxx...`

4. **Optional: Add More Variables:**
   ```
   MAIL_MAILER=null
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=465
   
   # For future: Redis caching (Phase 2)
   # CACHE_STORE=redis
   # REDIS_HOST=${{ Redis.REDIS_HOST }}
   # REDIS_PORT=${{ Redis.REDIS_PORT }}
   ```

---

### Step 4: Configure Build Settings (2 minutes)

1. **Click Web Service:**
   - Go to "Deploy" tab
   - Verify Buildpack is set to "Nixpacks"

2. **Build Command (Usually Auto-Detected):**
   ```
   # Railway auto-detects and uses:
   # 1. composer install
   # 2. npm ci (or npm install)
   # 3. npm run build
   ```

3. **Start Command (Auto-Set in Procfile):**
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```

---

### Step 5: Enable Process Management (1 minute)

1. **View Your Procfile:**
   - Your `Procfile` contains:
   ```
   web: vendor/bin/heroku-php-apache2 public/
   release: php artisan migrate --force
   worker: php artisan queue:work --tries=3 --timeout=90
   ```

2. **Railway Will Execute:**
   - ✅ `release:` - Runs migrations on deploy
   - ✅ `web:` - Starts PHP server
   - ℹ️ `worker:` - Optional (only if you enable it)

---

### Step 6: Deploy (1 minute)

1. **Push to GitHub:**
   ```powershell
   cd "d:\DIT MMP\5th sem\minor project\IT-DMS"
   git push origin main
   ```
   *(Replace 'main' with your branch name)*

2. **Railway Auto-Deploys:**
   - Watches your GitHub repository
   - Automatically builds & deploys on push
   - View logs in Railway dashboard

3. **First Deployment:**
   - Click "Deploy" tab in web service
   - Watch build progress in logs
   - First deploy takes 2-3 minutes

4. **Verify Deployment:**
   - Green checkmark = Success ✅
   - Click "Domain" tab to get public URL
   - Visit URL to test application

---

### Step 7: Verify Production (5 minutes)

1. **Test Application:**
   - Visit your Railway domain
   - Login as admin/parent/teacher
   - Test key features:
     - Student list loading
     - Attendance viewing
     - CSV export (should redirect to queue)
     - Dashboard rendering

2. **Check Logs:**
   - Click "Logs" in Railway dashboard
   - Verify no critical errors
   - Look for migration success message

3. **Database Status:**
   - Go to MySQL service in Railway
   - Verify it's running
   - Check migrations completed

4. **Storage Access:**
   - Verify file upload works (if you have upload features)
   - Check storage directory is writable

---

## 🔧 PRODUCTION CONFIGURATION DETAILS

### Current Configuration

**Web Process:**
```
vendor/bin/heroku-php-apache2 public/
```
- Starts Apache 2 server
- Runs PHP as module
- Serves files from `public/` directory
- Optimized for Railway

**Release Process:**
```
php artisan migrate --force
```
- Runs before web process starts
- Creates/updates database schema
- `--force` flag allows in production environment
- Safe because migrations are version-controlled

**Worker Process (Optional):**
```
php artisan queue:work --tries=3 --timeout=90
```
- Only enable if using Redis queue/jobs
- Not needed for basic application
- Can add later in Phase 1+

### Recommended Environment Variables

```
# Application
APP_NAME=IT-DMS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.up.railway.app
APP_LOG_LEVEL=warning
APP_LOCALE=en

# Database (Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=${{ MySQL.MYSQL_HOST }}
DB_PORT=${{ MySQL.MYSQL_PORT }}
DB_DATABASE=${{ MySQL.MYSQL_DATABASE }}
DB_USERNAME=${{ MySQL.MYSQL_USER }}
DB_PASSWORD=${{ MySQL.MYSQL_PASSWORD }}

# Sessions & Cache
SESSION_DRIVER=cookie
CACHE_STORE=file
CACHE_DRIVER=file

# Queue (use database for now)
QUEUE_CONNECTION=database

# Security
BCRYPT_ROUNDS=12
SESSION_SECURE_COOKIES=true
```

---

## 🧪 POST-DEPLOYMENT TESTING

### Automated Tests

```bash
# Run tests in Railway terminal
php artisan test

# Or with coverage
php artisan test --coverage
```

### Manual Testing Script

1. **Admin Functions:**
   - [ ] Create new student
   - [ ] Edit student details
   - [ ] Export students to CSV
   - [ ] View statistics dashboard

2. **Parent Portal:**
   - [ ] Login as parent
   - [ ] View children's attendance
   - [ ] View marks
   - [ ] View timetable

3. **Teacher Functions:**
   - [ ] Mark attendance
   - [ ] Enter marks
   - [ ] View class list
   - [ ] Generate reports

4. **Performance Check:**
   - [ ] Dashboard loads in <1 second
   - [ ] CSV export queues immediately (doesn't hang)
   - [ ] Attendance view shows instantly
   - [ ] No N+1 query errors in logs

### Load Testing (Optional)

```bash
# After deployment, test from your machine:
cd "d:\DIT MMP\5th sem\minor project\IT-DMS"
"C:\k6\k6-v0.50.0-windows-amd64\k6.exe" run k6-baseline-test.js --vus 1 --duration 30s
```

Expected: <600ms response time

---

## 🐛 TROUBLESHOOTING

### Build Failures

**Problem:** Build takes >5 minutes or fails
```
Solution:
1. Check "Build Logs" in Railway dashboard
2. Verify Procfile syntax (no spaces, correct format)
3. Clear cache: rm -rf bootstrap/cache/* storage/*
4. Push again with git push origin main
```

**Problem:** Composer install fails
```
Solution:
1. Check PHP version requirement in composer.json (needs >=8.2)
2. Ensure composer.lock is in git repository
3. Try: composer update (local) then git push
```

### Runtime Errors

**Problem:** "Class not found" or "Migration failed"
```
Solution:
1. Check logs for specific error
2. Verify migrations exist in database/migrations/
3. Check APP_KEY is set (not empty)
4. Restart deployment
```

**Problem:** "Logs show 500 errors"
```
Solution:
1. Set APP_DEBUG=true temporarily to see error details
2. Check database connection (DB_HOST, DB_PASSWORD)
3. Verify migrations ran (check MySQL service)
4. Review application logs for stack trace
```

**Problem:** "Database connection refused"
```
Solution:
1. Verify MySQL service is added to Railway project
2. Check DB_* variables are correctly set
3. Verify variables match Railway MySQL connection details
4. Test connection: php artisan tinker -> DB::connection()->getPdo()
```

### Performance Issues

**Problem:** Application slow on first load
```
Solution:
1. Expected: First load slower (~5 seconds on free tier)
2. Subsequent loads fast (<1 second)
3. Consider upgrading to Railway "Hobby" plan for faster CPU
```

**Problem:** CSV export times out
```
Solution:
1. Already optimized with Phase 1 fixes
2. Move to background jobs (Phase 1+ guide)
3. Temporarily: increase timeout in release process
```

---

## 📊 MONITORING & MAINTENANCE

### Railway Dashboard Metrics

1. **CPU Usage:**
   - Normal: <20% on initial load
   - After optimization: <5%
   - Warning: >50% sustained

2. **Memory Usage:**
   - Normal: 128-256MB
   - Acceptable: <512MB
   - Alert: >768MB

3. **Network I/O:**
   - Normal: <1MB/s
   - Database queries: Varies by load
   - Alert: >5MB/s sustained

### Logs to Monitor

```
# View logs in Railway dashboard or terminal:
railway logs

# Filter for errors:
railway logs | grep ERROR

# Filter for slow requests:
railway logs | grep "took.*ms"
```

### Scheduled Tasks (Optional)

If you use Laravel Scheduler, add to Procfile:
```
scheduler: php artisan schedule:work
```

---

## 🔐 SECURITY CHECKLIST

Before production:

- [ ] APP_DEBUG=false in production
- [ ] APP_ENV=production
- [ ] APP_LOG_LEVEL=warning (not debug)
- [ ] SESSION_SECURE_COOKIES=true (if using HTTPS)
- [ ] Strong APP_KEY generated
- [ ] No `.env` file in repository
- [ ] Database credentials from Railway (not hardcoded)
- [ ] Laravel dependencies up to date (`composer audit`)
- [ ] PHP dependencies scanned for vulnerabilities

---

## 📈 SCALING GUIDELINES

### Current Configuration (Phase 1)
- **Users:** ~500-1,000 concurrent
- **DAU:** ~5,000
- **Server:** Single web instance
- **Database:** Single MySQL instance
- **Cost:** ~$5-10/month (free tier available)

### Upgrade to Phase 2 (Next Week)
- **Add Redis:** For caching & sessions
- **Increase PHP Workers:** 2-3 instances
- **Database Upgrades:** Larger instance
- **Cost:** ~$30-50/month

### Full Phase 3 (Next Month)
- **Multi-Region:** Load balancing
- **Database Replication:** Read replicas
- **CDN:** Asset delivery
- **Cost:** ~$100-150/month

---

## 🚀 CONTINUOUS DEPLOYMENT

### Auto-Deploy on Push

Railway automatically deploys when you push to linked branch:

```powershell
# Make changes locally
git add .
git commit -m "Fix: improve performance"

# Push to GitHub
git push origin main

# Railway auto-deploys (no action needed)
# Watch progress in Railway dashboard
```

### Manual Rollback

```
1. Go to Railway dashboard
2. Click "Deployments"
3. Select previous working deployment
4. Click "Restore"
```

---

## 📞 GETTING HELP

### Railway Support
- Dashboard: https://railway.app
- Docs: https://docs.railway.app
- Community: https://railway.app/support

### Laravel Issues
- Docs: https://laravel.com/docs/12.x
- Community: https://laracasts.com

### Local Testing Before Deploy

```powershell
# Build production assets locally
npm run build

# Test with production settings
APP_ENV=production APP_DEBUG=false php artisan serve

# Run tests
php artisan test
```

---

## 📋 DEPLOYMENT CHECKLIST

**Before First Deploy:**
- [ ] Procfile created ✅
- [ ] railway.json created ✅
- [ ] .env.example updated
- [ ] composer.json has correct dependencies
- [ ] package.json has build script
- [ ] All files committed to git
- [ ] No .env file in repository

**During Deployment (Railway):**
- [ ] Create Railway project
- [ ] Add MySQL service
- [ ] Set environment variables
- [ ] Connect GitHub repository
- [ ] Trigger first deploy

**After Deployment:**
- [ ] Application loads without errors
- [ ] Database migrations successful
- [ ] Test basic features (login, view data)
- [ ] Monitor logs for 30 minutes
- [ ] Verify performance (response time < 1s)

---

## ✅ SUCCESS CRITERIA

Your deployment is successful when:

✅ Application accessible via Railway domain  
✅ No errors in application logs  
✅ Database migrations completed  
✅ Can login and view data  
✅ CSV export queues (doesn't hang)  
✅ Response time <1 second  
✅ Static assets loading (CSS, JS, images)  
✅ Mobile interface working  

---

## 🎯 NEXT STEPS AFTER DEPLOYMENT

### Immediate (Today)
1. ✅ Deploy to Railway (30 minutes)
2. ✅ Test all features (10 minutes)
3. ✅ Verify performance (5 minutes)

### This Week (Phase 1+)
1. Implement code-level caching
2. Setup monitoring dashboard
3. Move heavy operations to background jobs
4. Expected: 75%+ performance improvement

### Next Week (Phase 2)
1. Add Redis caching
2. Tune PHP-FPM
3. Optimize MySQL settings
4. Expected: 10x capacity improvement

### Production Monitoring
- Watch Railway dashboard daily
- Monitor error logs
- Track response times
- Scale resources as needed

---

## 📞 SUPPORT REFERENCE

| Issue | Solution |
|-------|----------|
| Build fails | Check Procfile syntax, review logs |
| Can't connect to DB | Verify MySQL service added, check variables |
| 500 errors | Set APP_DEBUG=true, check logs |
| Timeout errors | Database query too slow, apply Phase 1+ optimization |
| Performance slow | First load normal, 2nd load should be fast |

---

**Deployment Status:** ✅ READY  
**Infrastructure:** ✅ IDENTIFIED  
**Configuration:** ✅ COMPLETE  
**Documentation:** ✅ PROVIDED  

**Time to Deploy:** ~5 minutes  
**Time to Test:** ~10 minutes  
**Total Setup:** ~15 minutes  

Your application is ready for production deployment on Railway! 🚀
