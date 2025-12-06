#!/bin/bash
set -e


COMMAND="$@"


echo "🏁 Entrypoint started with command: $COMMAND"


# Si Composer → pas besoin de DB
if [[ "$1" == "composer" ]]; then
echo "📦 Composer command — skipping MySQL checks"
exec "$@"
fi


# Si PHPUnit → utiliser MySQL
if [[ "$1" == *"phpunit"* ]]; then
echo "🧪 PHPUnit detected — will use MySQL"
fi


# === Attendre MySQL ===
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
until nc -z "$DB_HOST" "$DB_PORT"; do
echo "MySQL not ready yet..."
sleep 2
done
echo "✅ MySQL is available."
fi


# === Migrations ===
if [ -f /var/www/html/artisan ]; then
echo "🔄 Running migrations..."
php artisan migrate --force || true
fi


echo "🚀 Running command: $COMMAND"
exec $COMMAND