echo "=== STAGE: Run Laravel tests (phpunit) ==="

sh '''
set -eux

# Nettoyer d’anciens conteneurs DB qui cassent docker-compose V1
docker rm -f ticklab_db || true
docker volume rm ticklab_db-data || true

# Démarrer la DB propre
docker compose up -d db

# Lancer les tests Laravel
docker compose run --rm -T \
    -e CI=true \
    -e APP_ENV=testing \
    app sh -c '
        cp .env.testing .env &&
        mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs &&
        chmod -R 777 storage &&
        php artisan migrate:fresh --seed --force &&
        vendor/bin/phpunit --configuration phpunit.xml
    '
'''
