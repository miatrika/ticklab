echo "=== STAGE: Run Laravel tests (phpunit) ==="

sh '''
set -eux

# Nettoyer anciens containers et volumes

docker compose down -v --remove-orphans || true

# Démarrer la DB propre

docker compose up -d db

# Attendre que la DB soit prête

until docker compose exec db mysqladmin ping -h "localhost" --silent; do
echo "Waiting for DB to be ready..."
sleep 2
done

# Lancer les tests Laravel via le service app

docker compose run --rm -T app sh -c "
cp .env.testing .env &&
mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs &&
chmod -R 777 storage &&
php artisan migrate:fresh --seed --force &&
vendor/bin/phpunit --configuration phpunit.xml
"
'''
