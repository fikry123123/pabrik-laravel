#!/bin/bash
set -e

echo "Menjalankan Database Migrations..."
php artisan migrate --force

echo "Membersihkan dan merestart cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Memulai Server Apache..."
exec apache2-foreground
