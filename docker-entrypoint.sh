#!/bin/bash

set -e

# Clear config cache just in case
php artisan config:clear

# Run Laravel migrations
php artisan migrate --force

# Create storage link if not exists
if [ ! -L "/var/www/html/public/storage" ]; then
  php artisan storage:link
fi

# Ensure permissions
chown -R www-data:www-data storage bootstrap/cache public/storage
chmod -R ug+rwX storage bootstrap/cache public/storage

# Launch Apache
exec "$@"




# #!/bin/bash

# # Run migrations and storage link
# php artisan config:clear
# php artisan migrate --force
# php artisan storage:link || true

# # Fix permissions just in case
# chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# # Start Apache
# exec "$@"
