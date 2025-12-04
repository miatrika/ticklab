#!/bin/bash
set -e

# === DÉTECTION DES COMMANDES STATIC ANALYSIS ===
# Ne pas attendre MySQL pour les commandes d'analyse statique
if [[ -n "$1" ]]; then
    if [[ "$1" == *"phpcs"* ]] || [[ "$1" == *"phpstan"* ]] || \
       [[ "$*" == *"phpcs"* ]] || [[ "$*" == *"phpstan"* ]] || \
       [[ "$1" == *"php-cs-fixer"* ]] || [[ "$*" == *"php-cs-fixer"* ]]; then
        echo "🔍 Static analysis command detected - skipping database operations"
        SKIP_DB_WAIT=true
        SKIP_MIGRATIONS=true
    fi
fi

# === ATTENTE MYSQL (CONDITIONNELLE) ===
if [ "${SKIP_DB_WAIT:-false}" != "true" ] && [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
    max_attempts=30
    attempt=1
    
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        if [ $attempt -ge $max_attempts ]; then
            echo "❌ MySQL not available after $max_attempts attempts"
            exit 1
        fi
        echo "Attempt $attempt/$max_attempts: MySQL not ready yet..."
        sleep 2
        attempt=$((attempt + 1))
    done
    echo "✅ MySQL is up and running."
elif [ "${SKIP_DB_WAIT:-false}" = "true" ]; then
    echo "⏭️ Skipping MySQL wait for static analysis"
else
    echo "⚠️ DB_HOST or DB_PORT not set, skipping database connection check"
fi

# === MIGRATIONS (CONDITIONNELLES) ===
if [ "${SKIP_MIGRATIONS:-false}" != "true" ] && [ -f /var/www/html/artisan ]; then
    echo "🔄 Running migrations..."
    php artisan migrate --force || echo "⚠️ Migrations failed or already applied"
else
    echo "⏭️ Skipping migrations."
fi

# === GESTION ENVIRONNEMENT CI/CD ===
if [ "$CI" = "true" ] || [ "${SKIP_DB_WAIT:-false}" = "true" ]; then
    echo "🏗️ CI/test environment detected — skipping PHP-FPM start."
    
    # Si une commande a été passée (ex: composer, phpcs, phpstan, tests)
    if [ -n "$1" ]; then
        echo "🚀 Executing command: $@"
        exec "$@"
    else
        echo "ℹ️ No command provided. Exiting cleanly."
        exit 0
    fi
else
    echo "🚀 Starting PHP-FPM..."
    exec php-fpm -F
fi