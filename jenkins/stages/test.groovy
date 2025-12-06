echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1️⃣ Démarrer les conteneurs nécessaires (DB + app)
docker compose up -d db app

# 2️⃣ Attendre que MySQL soit prêt
echo "Waiting for MySQL..."
for i in {1..20}; do
    docker compose exec -T app bash -c "nc -z db 3306" && echo "MySQL is ready" && break
    echo "MySQL not ready yet..."
    sleep 2
done

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

# 4️⃣ Installer les dépendances (si nécessaire)
docker compose exec -T app composer install --no-interaction --prefer-dist

# 5️⃣ Forcer Laravel à utiliser .env.testing
docker compose exec -T app php artisan config:clear --env=testing
docker compose exec -T app php artisan cache:clear --env=testing

# 6️⃣ Exécuter migrations en mode testing
docker compose exec -T app php artisan migrate:fresh --force --env=testing

# 7️⃣ Lancer PHPUnit avec le bon env
docker compose exec -T app vendor/bin/phpunit --configuration phpunit.xml --testdox --env=testing
'''

echo "✅ Tests completed successfully."
