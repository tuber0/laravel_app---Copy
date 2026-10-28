#!/usr/bin/env bash
echo "Running composer install..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Ensuring storage and cache directories exist with correct permissions..."
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Running database migrations..."
php artisan migrate --force || echo "Warning: Migration failed or database not reachable. Please verify DB credentials in Render environment."
