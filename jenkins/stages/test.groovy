echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1) Démarrer DB
docker compose up -d db

# 2) Attendre MySQL (timeout simple)
echo "Waiting for MySQL..."
MAX_TRIES=30
i=0
until docker compose exec -T db bash -c "mysqladmin ping -uroot -p${DB_PASSWORD:-15182114} > /dev/null 2>&1"; do
    i=$((i+1))
    if [ "$i" -ge "$MAX_TRIES" ]; then
        echo "MySQL did not become ready in time."
        docker compose ps
        exit 1
    fi
    echo "MySQL not ready yet..."
    sleep 2
done
echo "MySQL is ready!"

# 3) Démarrer app
docker compose up -d app

# 4) Créer .env.testing (avec CACHE_DRIVER=array pour éviter besoin de table cache)
docker compose exec -T app bash -lc "cat > .env.testing <<'EOF'
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
DB_PASSWORD=${DB_PASSWORD:-15182114}

# IMPORTANT: use array cache/session for CI so no DB table needed
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
"

# 5) Forcer artisan à utiliser l'env de test pour ces commandes en exportant APP_ENV et autres variables
#    (on évite d'utiliser --env car ce n'est pas toujours fiable pour charger .env.testing)
docker compose exec -T app bash -lc "
export APP_ENV=testing
export APP_KEY='base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo='
export DB_CONNECTION='mysql'
export DB_HOST='db'
export DB_PORT='3306'
export DB_DATABASE='ticklab'
export DB_USERNAME='root'
export DB_PASSWORD='${DB_PASSWORD:-15182114}'
export CACHE_DRIVER='array'
export SESSION_DRIVER='array'
# 6) Exécuter les migrations AVANT toute commande qui toucherait la table cache
php artisan migrate:fresh --force || { echo 'Migrations failed'; php artisan migrate:status; exit 1; }
# 7) Ensuite on clear config/cache en sécurité (cache driver = array)
php artisan config:clear
php artisan cache:clear || true
# 8) Lancer les tests
vendor/bin/phpunit --configuration phpunit.xml --testdox
"

echo "✅ Tests completed successfully."
'''
