#!/bin/bash
set -e

echo "🏁 Entrypoint started"

# Si Composer → pas besoin de DB
if [[ "$1" == "composer" ]]; then
    echo "📦 Composer command — skipping MySQL checks"
    exec "$@"
fi

# Attendre MySQL
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
    until nc -z "$DB_HOST" "$DB_PORT"; do
        echo "MySQL not ready yet..."
        sleep 2
    done
    echo "✅ MySQL is available."
fi

# Lancer les migrations
if [ -f /var/www/html/artisan ]; then
    echo "🔄 Running migrations..."
    php artisan migrate --force || true
fi

# Démarrer PHP-FPM en foreground
echo "🚀 Starting PHP-FPM..."
exec php-fpm
