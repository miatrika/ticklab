echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1️⃣ Démarrer les conteneurs DB et App
docker compose up -d db app

# 2️⃣ Attendre que MySQL soit prêt
echo "Waiting for MySQL..."
MAX_TRIES=20
i=0
until docker compose exec -T db bash -c "mysqladmin ping -uroot -p15182114 > /dev/null 2>&1"; do
    i=$((i+1))
    if [ "$i" -ge "$MAX_TRIES" ]; then
        echo "MySQL did not become ready in time."
        exit 1
    fi
    echo "MySQL not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

# 3️⃣ Préparer le .env.testing
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

# 4️⃣ Installer les dépendances Composer
docker compose exec -T app bash -c "export COMPOSER_MEMORY_LIMIT=-1; composer install --no-interaction --prefer-dist"

# 5️⃣ Clear config & cache
docker compose exec -T app php artisan config:clear --env=testing
docker compose exec -T app php artisan cache:clear --env=testing

# 6️⃣ Migrations
docker compose exec -T app php artisan migrate:fresh --force --env=testing

# 7️⃣ Lancer les tests PHPUnit
docker compose exec -T app vendor/bin/phpunit --configuration phpunit.xml --testdox --env=testing
'''

echo "✅ Tests completed successfully."
