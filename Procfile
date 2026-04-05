release: php artisan migrate --force --no-interaction && php artisan config:cache && php artisan route:cache
web: php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
