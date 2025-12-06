echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# ❗ Ne PAS supprimer les volumes de la DB pour MySQL
docker compose down --remove-orphans || true
# Forcer la création du réseau et maintenir "db" actif
docker compose up -d db

# Installer les dépendances dans l'image app
docker compose run --rm -T app composer install --no-interaction --prefer-dist

# Préparer le .env.testing pour MySQL
docker compose run --rm -T app bash -c "
cat > .env.testing << 'EOF'
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
CI=true

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ticklab
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
"

# Nettoyage Laravel
docker compose run --rm -T app php artisan config:clear
docker compose run --rm -T app php artisan cache:clear

# Démarrer la DB propre
docker compose up -d db

# Vérifier que MySQL est up
docker compose run --rm -T app bash -c "
    echo 'Waiting for MySQL...';
    for i in {1..20}; do
        nc -z db 3306 && echo 'MySQL is ready' && exit 0;
        echo 'MySQL not ready yet...';
        sleep 2;
    done;
    echo 'MySQL startup timeout'; exit 1;
"

# Migrations MySQL
docker compose run --rm -T app php artisan migrate:fresh --force --env=testing

# Exécuter PHPUnit
docker compose run --rm -T app vendor/bin/phpunit --configuration phpunit.xml --testdox
'''

echo "✅ Tests completed successfully."