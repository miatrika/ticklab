#!/bin/bash
set -e

echo "🏁 Entrypoint started with command: $@"

# Si Composer → pas besoin de DB
if [[ "$1" == "composer" ]]; then
    echo "📦 Composer command — skipping MySQL checks"
    exec "$@"
fi

# Si PHPUnit → utiliser MySQL
if [[ "$1" == *"phpunit"* ]]; then
    echo "🧪 PHPUnit detected — will use MySQL"
fi

# === Attendre MySQL si défini ===
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
    until nc -z "$DB_HOST" "$DB_PORT"; do
        echo "MySQL not ready yet..."
        sleep 2
    done
    echo "✅ MySQL is available."
fi

# === Migrations Laravel ===
if [ -f /var/www/html/artisan ]; then
    echo "🔄 Running migrations..."
    php artisan migrate --force || true
fi

# === Lancement du process principal ===
if [ $# -eq 0 ]; then
    echo "🚀 No command provided, starting PHP-FPM..."
    exec php-fpm
else
    echo "🚀 Running command: $@"
    exec "$@"
fi
