web: vendor/bin/heroku-php-apache2 public/
release: mkdir -p storage/framework/{cache,sessions,views} && php artisan migrate --force && php artisan config:cache
worker: php artisan queue:work --tries=3 --timeout=90
