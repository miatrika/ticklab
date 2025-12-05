#!/bin/bash
set -e

# === DÉTECTION DES COMMANDES QUI N'ONT PAS BESOIN DE MYSQL ===
if [[ -n "$1" ]]; then
    # Commandes d'analyse statique
    if [[ "$1" == *"phpcs"* ]] || [[ "$*" == *"phpcs"* ]] || \
       [[ "$1" == *"phpstan"* ]] || [[ "$*" == *"phpstan"* ]] || \
       [[ "$1" == *"php-cs-fixer"* ]] || [[ "$*" == *"php-cs-fixer"* ]]; then
        echo "🔍 Static analysis command detected - skipping database operations"
        SKIP_DB_WAIT=true
        SKIP_MIGRATIONS=true
    
    # Commandes de test PHPUnit - IMPORTANT: garder les migrations pour SQLite!
    elif [[ "$1" == *"phpunit"* ]] || [[ "$*" == *"phpunit"* ]] || \
         [[ "$1" == *"test"* ]] || [[ "$*" == *"test"* ]]; then
        echo "🧪 Test command detected - checking database configuration..."
        
        # Vérifier si on utilise SQLite
        if [ -f /var/www/html/.env ]; then
            if grep -q "DB_CONNECTION=sqlite" /var/www/html/.env; then
                echo "📁 SQLite detected in .env - skipping MySQL wait but KEEPING migrations"
                SKIP_DB_WAIT=true
                # IMPORTANT: On garde les migrations pour les tests SQLite!
                SKIP_MIGRATIONS=false
            elif [ "$DB_CONNECTION" = "sqlite" ] || [ "${DB_CONNECTION:-}" = "sqlite" ]; then
                echo "📁 SQLite detected in environment - skipping MySQL wait but KEEPING migrations"
                SKIP_DB_WAIT=true
                SKIP_MIGRATIONS=false
            else
                echo "🗄️ MySQL detected for tests - will wait for database"
                SKIP_MIGRATIONS=false
            fi
        else
            echo "📝 No .env file - checking environment variables"
            SKIP_MIGRATIONS=false
        fi
    
    # Commandes Composer (install, update, etc.)
    elif [[ "$1" == "composer" ]]; then
        echo "📦 Composer command detected - skipping database operations"
        SKIP_DB_WAIT=true
        SKIP_MIGRATIONS=true
    fi
fi

# === DÉTECTION ENVIRONNEMENT CI/TEST ===
if [ "$CI" = "true" ] || [ "$APP_ENV" = "testing" ] || [ "${APP_ENV:-}" = "testing" ]; then
    echo "🏗️ CI/Test environment detected"
    
    # En mode test, vérifier si on a besoin de MySQL
    if [ -f /var/www/html/.env ]; then
        if grep -q "DB_CONNECTION=sqlite" /var/www/html/.env || \
           [ "$DB_CONNECTION" = "sqlite" ] || \
           [ "${DB_CONNECTION:-}" = "sqlite" ]; then
            echo "📁 Test environment with SQLite - skipping MySQL but keeping migrations"
            SKIP_DB_WAIT=true
            # Garder les migrations pour SQLite en test!
            SKIP_MIGRATIONS=false
        else
            echo "🗄️ Test environment with MySQL - will wait for database"
            SKIP_MIGRATIONS=false
        fi
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
            
            # En mode CI, ne pas faire échouer si c'est pour des tests
            if [ "$CI" = "true" ] || [ "$APP_ENV" = "testing" ]; then
                echo "⚠️ CI/Test mode - continuing without MySQL"
                SKIP_DB_WAIT=true
                # Si on utilise SQLite, on garde les migrations
                if [ "$DB_CONNECTION" = "sqlite" ] || [ "${DB_CONNECTION:-}" = "sqlite" ]; then
                    SKIP_MIGRATIONS=false
                fi
                break
            else
                exit 1
            fi
        fi
        echo "Attempt $attempt/$max_attempts: MySQL not ready yet..."
        sleep 2
        attempt=$((attempt + 1))
    done
    
    if [ "${SKIP_DB_WAIT:-false}" != "true" ]; then
        echo "✅ MySQL is up and running."
    fi
    
elif [ "${SKIP_DB_WAIT:-false}" = "true" ]; then
    echo "⏭️ Skipping MySQL wait"
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
if [ "$CI" = "true" ] || [ "${SKIP_DB_WAIT:-false}" = "true" ] || [ "$APP_ENV" = "testing" ]; then
    echo "🏗️ CI/Test environment detected — executing command directly."
    
    # Si une commande a été passée
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