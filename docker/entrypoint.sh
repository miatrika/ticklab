#!/bin/bash
set -e

# Wait for DB
echo "Waiting for MySQL..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done

echo "MySQL is up."

# Run migrations only if artisan exists
if [ -f /var/www/html/artisan ]; then
  echo "Running migrations..."
  php artisan migrate --force || true
else
  echo "No artisan file found, skipping migrations."
fi

# Start PHP-FPM
php-fpm -F
