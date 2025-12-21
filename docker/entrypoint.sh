#!/bin/sh

# 1. Pastikan folder storage punya permission yang benar
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

# 2. Link Storage (Agar file yang diupload ke storage bisa diakses publik)
php artisan storage:link --force

# 3. Cache Configuration (Untuk performa production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Jalankan Supervisor (Start Nginx + PHP)
exec "$@"