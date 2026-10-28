#!/usr/bin/env bash
echo "Running composer install..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Running database migrations..."
php artisan migrate --force || echo "Warning: Migration failed or database not reachable. Please verify DB credentials in Render environment."
