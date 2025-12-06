echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# ⚠️ Ne JAMAIS supprimer les volumes !
docker compose down --remove-orphans || true

# Installer les dépendances dans l'image app
docker compose run --rm -T app composer install --no-interaction --prefer-dist

# Préparer le .env de test
docker compose run --rm -T app bash -c "
cat > .env.testing << 'EOF'
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
CI=true

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/testing.sqlite

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
"

# Créer la base sqlite
docker compose run --rm -T app bash -c "
    mkdir -p database
    touch database/testing.sqlite
    chmod 666 database/testing.sqlite
"

# Nettoyage Laravel
docker compose run --rm -T app php artisan config:clear
docker compose run --rm -T app php artisan cache:clear

# Migrations SQLite
docker compose run --rm -T app php artisan migrate:fresh --force --env=testing

# Exécuter PHPUnit
docker compose run --rm -T app vendor/bin/phpunit --configuration phpunit.xml --testdox
'''

echo "✅ Tests completed successfully."
