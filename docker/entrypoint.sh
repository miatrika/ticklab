#!/bin/bash
set -e

# Attendre que la DB soit prête
echo "Waiting for MySQL..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done

echo "MySQL is up."

# Exécuter les migrations Laravel seulement si artisan existe
if [ -f /var/www/html/artisan ]; then
  echo "Running migrations..."
  php artisan migrate --force || true
else
  echo "No artisan file found, skipping migrations."
fi

# Si on est en CI, on ne démarre pas PHP-FPM pour éviter de bloquer Jenkins
if [ "$CI" != "true" ]; then
  echo "Starting PHP-FPM..."
  php-fpm -F
else
  echo "CI environment detected, skipping php-fpm start."
fi
