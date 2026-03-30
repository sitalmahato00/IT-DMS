# 419 Login Fix TODO

## Current Status
- [x] Step 1: Temporarily change config/session.php SESSION_DRIVER to 'file' ✓
- [x] Step 2: Run php artisan config:clear, route:clear, view:clear ✓ (cache:clear DB fail expected)
- [x] Step 3: Delete storage/framework/sessions/* files (cmd adjusted for Windows) ✓
- [ ] Step 4: Test login (run dev server if not running: php artisan serve)
- [ ] Step 5: Long-term: Fix .env DB_PASSWORD (check DOCKER_SETUP.md), docker-compose up -d, php artisan migrate, revert driver to 'database'

**Next Action:** Edit config/session.php to use file driver.

**Root Cause:** Laravel SESSION_DRIVER='database' but MySQL connection fails (no DB_PASSWORD, Docker network issue). CSRF token can't persist, causing 419 on login POST.

**Verification:** After file driver, login should work. Then fix DB for production.
