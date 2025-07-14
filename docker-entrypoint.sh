#!/bin/bash

# Run migrations and storage link
php artisan config:clear
php artisan migrate --force
php artisan storage:link || true

# Fix permissions just in case
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# Start Apache
exec "$@"
