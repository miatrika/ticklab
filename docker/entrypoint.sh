#!/bin/bash
set -e

# Wait for DB
echo "Waiting for MySQL..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done

echo "MySQL is up. Running migrations..."

# Run migrations
php artisan migrate --force || true

# Start PHP-FPM
php-fpm -F
