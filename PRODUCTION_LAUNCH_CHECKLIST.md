# ⚡ PRODUCTION READINESS - QUICK REFERENCE CHECKLIST

## 🎯 BEFORE LAUNCH CHECKLIST (Print this!)

### Phase 1: Apply Critical Fixes (8-10 hours)
- [ ] Fix SQL injection in search filters (45 min)
  - Files: CourseController, StudentController
  - Guide: See CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md line 20-60
  
- [ ] Add N+1 query eager loading (2 hours)
  - Files: All admin controllers
  - Guide: See CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md line 80-180
  
- [ ] Create database indexes (15 min)
  - Run migration: `php artisan make:migration add_indexes_to_tables`
  - Guide: See CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md line 240-295
  
- [ ] Enable session encryption (5 min)
  - Change: `.env` → `SESSION_ENCRYPT=true`
  - Also set: `SESSION_SECURE_COOKIE=true`
  
- [ ] Fix XSS vulnerabilities (45 min)
  - Search: `{!!` in all blade files
  - Replace with: `{{` unless trusted HTML
  - Guide: See CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md line 320-345
  
- [ ] Add rate limiting (15 min)
  - File: `routes/api.php`
  - Guide: See CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md line 365-385

### Phase 2: Write Tests (70+ required)
- [ ] Authentication tests (12 tests - 2 hours)
- [ ] Course management tests (10 tests - 1.5 hours)
- [ ] Marks entry tests (15 tests - 2.5 hours)
- [ ] API tests (15 tests - 2 hours)
- [ ] Security tests (10 tests - 1.5 hours)
- [ ] Attendance tests (10 tests - 1.5 hours)
- [ ] Report tests (12 tests - 2 hours)
- [ ] Remaining (5+ tests - 1 hour)

**Total: 70+ tests = ~14-16 hours**

Run tests:
```bash
php artisan test
php artisan test --coverage  # Min 60%
```

### Phase 3: Verify Fixes
- [ ] No SQL injection vulnerabilities
  - Test: `' OR '1'='1` in search - should be safe
  - Verify: Check CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md examples
  
- [ ] Query count < 20 for dashboard
  - Enable: DEBUG in dev environment
  - Check: Debugbar query count
  
- [ ] Page load time < 500ms
  - Test: Use Chrome DevTools
  - Check: Network tab > Load time
  
- [ ] Error pages display correctly
  - Test: Visit `/404`, `/500` (with APP_DEBUG=false)
  - Verify: Custom error pages show, not debug screen
  
- [ ] Health endpoint working
  - Test: `curl http://localhost/health`
  - Verify: Returns 200 OK with JSON
  
- [ ] Session encrypted
  - Check: `.env` has `SESSION_ENCRYPT=true`
  - Verify: Session cookies not readable

### Phase 4: Deployment Prep
- [ ] Create `.env.production` from `.env.production.example`
  - Update: Database credentials
  - Update: Mail credentials
  - Set: APP_URL correctly
  - Generate: APP_KEY with `php artisan key:generate`
  
- [ ] Database backup created
  - Command: `mysqldump -u user -p database > backup.sql`
  
- [ ] Storage symlink tested
  - Command: `php artisan storage:link`
  - Verify: `public/storage` links to `storage/app/public`
  
- [ ] Deployment script tested on staging
  - Command: `bash deploy-production.sh`
  - Verify: No errors, all steps complete
  
- [ ] Rollback plan documented
  - Keep: Database backups (30 days)
  - Keep: Code backups (last 5 releases)
  - Test: Rollback procedure works

### Phase 5: Pre-Launch Testing
- [ ] Login as admin - verify works
- [ ] Login as teacher - verify works
- [ ] Login as student - verify works
- [ ] Login as parent - verify works
- [ ] Create a course - verify works
- [ ] Enroll student - verify works
- [ ] Mark attendance - verify works
- [ ] Enter marks - verify works
- [ ] Generate report - verify works
- [ ] Export to Excel - verify works
- [ ] Test on mobile (actual device)
  - iPhone 12/SE ✅
  - Android device ✅
  - Tablet orientation ✅

### Phase 6: Final Checks
- [ ] APP_DEBUG = false in production
- [ ] APP_ENV = production in production
- [ ] Monitoring/logging configured
- [ ] Error alerts configured (Sentry/email)
- [ ] Database backups automated
- [ ] SSL certificate valid
- [ ] HTTPS redirect configured
- [ ] CORS headers correct
- [ ] Caching enabled
- [ ] Queue configured correctly

---

## 🚀 DEPLOYMENT DAY CHECKLIST

### Pre-Deployment (30 minutes before)
- [ ] 1. Stop background jobs: `php artisan queue:pause`
- [ ] 2. Create fresh database backup
- [ ] 3. Test deployment script on staging one more time
- [ ] 4. Notify stakeholders: "Deploying in 30 minutes"
- [ ] 5. Have rollback plan ready: `/path/to/rollback.sh`

### During Deployment (20-30 minutes)
- [ ] 1. Execute: `bash deploy-production.sh`
- [ ] 2. Monitor output for errors
- [ ] 3. Verify: `curl http://yourdomain.com/health`
- [ ] 4. Check logs: `tail -f storage/logs/laravel.log`
- [ ] 5. Resume jobs: `php artisan queue:resume`

### Post-Deployment (First 4 hours - continuous monitoring)
```
Every 15 minutes for first hour:
- [ ] Check error logs
- [ ] Monitor CPU usage
- [ ] Monitor memory usage
- [ ] Check response times
- [ ] Verify no error alerts

Every 30 minutes for hours 2-4:
- [ ] Continue monitoring above
- [ ] User testing
- [ ] Email verification
- [ ] File upload test
- [ ] Report generation test
```

**CRITICAL**: Do NOT go home until 4 hours of monitoring complete!

---

## 📱 MOBILE TESTING CHECKLIST

Test on these devices:
- [ ] iPhone 12/13/14 (Safari)
- [ ] iPhone SE (smaller screen)
- [ ] Android 6-12 (Chrome)
- [ ] Tablet in portrait
- [ ] Tablet in landscape

Test these flows:
- [ ] Login page layout
- [ ] Dashboard responsive
- [ ] Form inputs usable
- [ ] Buttons large enough (44x44px minimum)
- [ ] Images load correctly
- [ ] Tables scroll horizontally
- [ ] Modals display correctly
- [ ] Navigation responsive
- [ ] No overlapping text
- [ ] No horizontal scroll (except tables)

---

## 🔒 SECURITY VERIFICATION

**Before launch, test:**

- [ ] SQL Injection
  ```bash
  # Try in search: ' OR '1'='1
  # Should NOT return all records
  curl "https://yourdomain.com/admin/courses?search=' OR '1'='1"
  ```

- [ ] XSS
  ```bash
  # Try uploading file with name: <script>alert('xss')</script>
  # Should NOT execute
  ```

- [ ] CSRF
  ```bash
  # Make POST without CSRF token
  # Should get 419 error or redirect
  ```

- [ ] Rate Limiting
  ```bash
  # Make 100 requests in 1 minute to API
  for i in {1..100}; do curl http://api/courses; done
  # Should get 429 after limit
  ```

- [ ] Authentication
  ```bash
  # Try accessing admin without login
  curl https://yourdomain.com/admin
  # Should redirect to /login
  ```

---

## 📊 PERFORMANCE VERIFICATION

**Measure and document:**

- [ ] Dashboard load time: _____ ms (target: <500ms)
- [ ] Course list load time: _____ ms (target: <500ms)
- [ ] Report generation time: _____ min (target: <5 min for 1000 students)
- [ ] Login time: _____ ms (target: <300ms)
- [ ] Database queries for dashboard: _____ (target: <20)
- [ ] Memory usage: _____ MB (target: <400MB)
- [ ] CPU usage peak: _____ % (target: <70%)

---

## 📋 DOCUMENTATION CHECKLIST

Before launch, ensure team has:
- [ ] PRODUCTION_READINESS_AUDIT.md - Read & understood
- [ ] CRITICAL_FIXES_IMPLEMENTATION_GUIDE.md - Reference guide
- [ ] TESTING_STRATEGY.md - Test documentation
- [ ] deploy-production.sh - Deployment script
- [ ] Runbook for common issues
- [ ] Contact list for escalation
- [ ] Rollback procedure documented
- [ ] Monitoring dashboard setup

---

## 🆘 EMERGENCY PROCEDURES

### If site goes down:
```bash
# 1. Check logs
tail -f storage/logs/laravel.log

# 2. Check system
curl http://localhost/health

# 3. Restart services
systemctl restart php-fpm
systemctl restart redis-server

# 4. Rollback if needed
bash /backups/rollback.sh [backup-date]
```

### If you get database errors:
```bash
# 1. Check connection
php artisan tinker
> \DB::connection()->getPdo();

# 2. Check migrations
php artisan migrate:status

# 3. Rollback if needed
php artisan migrate:rollback
```

### If you get cache errors:
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## ✅ SIGN-OFF

**Ready for Production?**

- [ ] All critical fixes applied
- [ ] 70+ tests passing
- [ ] Test coverage 60%+
- [ ] Security audit passed
- [ ] Performance verified
- [ ] Mobile testing complete
- [ ] Deployment tested on staging
- [ ] Team trained
- [ ] Stakeholders notified

**Sign-off:**
- Developer: _________________ Date: _______
- Tech Lead: _________________ Date: _______
- Product Owner: _____________ Date: _______

**DO NOT DEPLOY WITHOUT ALL CHECKBOXES CHECKED**

---

## 📞 HELPFUL COMMANDS

```bash
# Run tests
php artisan test
php artisan test --coverage

# Check if site is healthy
curl http://localhost/health

# View logs in real-time
tail -f storage/logs/laravel.log

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database
php artisan migrate
php artisan migrate:refresh
php artisan db:seed

# Queue (if using)
php artisan queue:work
php artisan queue:restart

# Maintenance mode
php artisan down
php artisan up

# Clear all cache
php artisan cache:clear
php artisan config:clear

# Performance profiling
php artisan tinker
> \DB::enableQueryLog();
> // Run your code
> \DB::getQueryLog();
```

---

**Print this checklist and post at your desk during deployment!**

Last Updated: April 2026
