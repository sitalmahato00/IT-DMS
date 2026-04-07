# Deployment Update Guide

## Quick Update Commands

Run the following commands to update the project in production/deployment environment:

```bash
cd ~/laravel-app/IT-DMS
git pull origin main
```

## Clear Laravel Caches

After pulling updates, clear all caches and regenerate configuration:

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
```

## Complete Update Script

Run all commands together:

```bash
cd ~/laravel-app/IT-DMS && \
git pull origin main && \
php artisan optimize:clear && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan cache:clear && \
php artisan config:cache
```

## What Each Command Does

| Command | Purpose |
|---------|---------|
| `git pull origin main` | Pull latest updates from the main branch |
| `php artisan optimize:clear` | Clear all optimization caches |
| `php artisan config:clear` | Clear configuration cache |
| `php artisan route:clear` | Clear route cache |
| `php artisan view:clear` | Clear compiled views |
| `php artisan cache:clear` | Clear application cache |
| `php artisan config:cache` | Regenerate configuration cache |

## Important Notes

- Run these commands from the project root directory
- Ensure sufficient disk space before running cache operations
- These commands do not affect the database
- No downtime is required for these operations
- For zero-downtime deployment, consider using maintenance mode

## Optional: Enable Maintenance Mode

For zero-downtime updates:

```bash
# Enable maintenance mode
php artisan down

# Run update commands above

# Disable maintenance mode
php artisan up
```

## Troubleshooting

If issues persist after update:
1. Verify all commands executed successfully
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Re-run the cache clearing commands
4. Check file permissions in `storage/` and `bootstrap/cache/` directories
