#!/bin/bash
set -e

echo "Waiting for MySQL..."
while ! nc -z "$DB_HOST" "$DB_PORT"; do
  sleep 2
done
echo "MySQL is up."

if [ -f /var/www/html/artisan ]; then
  echo "Running migrations..."
  php artisan migrate --force || true
else
  echo "No artisan file found, skipping migrations."
fi

# 🔹 Si on est dans un environnement CI (Jenkins) ou une commande docker-compose run
if [ "$CI" = "true" ] || [ "$1" != "" ]; then
  echo "CI/test environment detected — skipping PHP-FPM start."
  exec "$@"  # exécute la commande demandée (phpcs, phpstan, etc.)
else
  echo "Starting PHP-FPM..."
  exec php-fpm -F
fi
