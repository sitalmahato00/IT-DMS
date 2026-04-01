# Docker Startup TODO for IT-DMS Laravel Project

## Completed:
- [x] 1. Start containers: docker-compose up -d --build ✓
- [x] 2. Copy .env ✓
- [x] 3. Generate app key ✓
## Pending:
- [ ] 4. Install Composer deps: docker-compose exec app composer install
- [ ] 4. Install Composer deps: docker-compose exec app composer install
- [ ] 5. Run migrations: docker-compose exec app php artisan migrate
- [ ] 6. Storage link: docker-compose exec app php artisan storage:link
- [ ] 7. Install npm deps: docker-compose exec app npm install
- [ ] 8. Start Vite dev server: docker-compose up -d vite
- [ ] 9. Verify: docker-compose ps && open http://localhost

Notes: 
- App runs on http://localhost
- DB: dit/laravel/laravel_password @ localhost:3306
- Logs: docker-compose logs -f
