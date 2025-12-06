echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1️⃣ Démarrer uniquement la DB
docker compose up -d db

# 2️⃣ Attendre que MySQL soit prêt
echo "Waiting for MySQL..."
until docker compose exec -T db bash -c "mysqladmin ping -uroot -p15182114 > /dev/null 2>&1"; do
    echo "MySQL not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

# 3️⃣ Démarrer le conteneur app maintenant qu'on sait que MySQL est prêt
docker compose up -d app

# 4️⃣ Préparer le .env.testing
docker compose exec -T app bash -c "cat > .env.testing << 'EOF'
APP_NAME=TickLab
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
CI=true

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ticklab
DB_USERNAME=root
DB_PASSWORD=15182114

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
"

# 5️⃣ Installer les dépendances
docker compose exec -T app composer install --no-interaction --prefer-dist

# 6️⃣ Clear cache & config
docker compose exec -T app php artisan config:clear --env=testing
docker compose exec -T app php artisan cache:clear --env=testing

# 7️⃣ Migrations
docker compose exec -T app php artisan migrate:fresh --force --env=testing

# 8️⃣ PHPUnit
docker compose exec -T app vendor/bin/phpunit --configuration phpunit.xml --testdox --env=testing
'''

echo "✅ Tests completed successfully."
