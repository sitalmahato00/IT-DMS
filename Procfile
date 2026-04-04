web: php -S 0.0.0.0:${PORT:-8000} -t public/
release: php artisan migrate --force && php artisan db:seed --force
