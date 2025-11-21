echo "=== STAGE: Run Laravel tests (phpunit) ==="

sh '''
set -eux

# Arrêter et supprimer tous les containers et volumes orphelins liés au projet

docker compose down -v --remove-orphans || true

# Démarrer la DB propre

docker compose up -d db

# Attendre que la DB soit prête

until docker compose exec db mysqladmin ping -h "localhost" --silent; do
echo "Waiting for DB to be ready..."
sleep 2
done

# Lancer les tests Laravel

docker compose run --rm -T 
-e CI=true 
-e APP_ENV=testing 
app sh -c '
cp .env.testing .env &&
mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs &&
chmod -R 777 storage &&
php artisan migrate:fresh --seed --force &&
vendor/bin/phpunit --configuration phpunit.xml
'
'''
