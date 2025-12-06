echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1️⃣ Démarrer DB et App
docker compose up -d db app

# 2️⃣ Attendre que MySQL soit prêt
until docker compose exec -T db bash -c "mysqladmin ping -uroot -p15182114 > /dev/null 2>&1"; do
    echo "MySQL not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

# 3️⃣ Préparer .env.testing
docker compose exec -T app bash -c "cat > .env.testing << 'EOF'
APP_NAME=TickLab
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:...
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

# 4️⃣ Installer les dépendances Composer
docker compose exec -T app bash -c "export COMPOSER_MEMORY_LIMIT=-1; composer install --no-interaction --prefer-dist"

# 5️⃣ Clear cache & config
docker compose exec -T app php artisan config:clear --env=testing
docker compose exec -T app php artisan cache:clear --env=testing

# 6️⃣ Migrations
docker compose exec -T app php artisan migrate:fresh --force --env=testing

# 7️⃣ PHPUnit
docker compose exec -T app vendor/bin/phpunit --configuration phpunit.xml --testdox --env=testing
'''

echo "✅ Tests completed successfully."
