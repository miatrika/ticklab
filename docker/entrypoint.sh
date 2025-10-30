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

# Vérifie si on est dans un environnement CI/CD
if [ "$CI" = "true" ]; then
  echo "CI/test environment detected — skipping PHP-FPM start."
  # Si une commande a été passée (ex: composer, phpcs, phpstan)
  if [ -n "$1" ]; then
    echo "Executing command: $@"
    exec "$@"
  else
    echo "No command provided. Exiting cleanly."
    exit 0
  fi
else
  echo "Starting PHP-FPM..."
  exec php-fpm -F
fi
