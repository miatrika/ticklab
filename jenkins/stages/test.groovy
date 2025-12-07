echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# Variables
DB_USER="root"
DB_PASSWORD="${DB_PASSWORD:-15182114}"
DB_NAME="ticklab"
DB_HOST="db"
APP_KEY="base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo="
MAX_TRIES=30

# 1) Démarrer DB
docker compose up -d db

# 2) Attendre MySQL
echo "Waiting for MySQL..."
i=0
until docker compose exec -T db bash -c "mysqladmin ping -u$DB_USER -p$DB_PASSWORD -h $DB_HOST > /dev/null 2>&1"; do
    i=$((i+1))
    if [ "$i" -ge "$MAX_TRIES" ]; then
        echo "MySQL did not become ready in time."
        docker compose ps
        exit 1
    fi
    echo "MySQL not ready yet... retry #$i"
    sleep 2
done
echo "MySQL is ready!"

# 3) Démarrer app
docker compose up -d app

# 4) Créer .env.testing
docker compose exec -T app bash -lc "cat > .env.testing <<'EOF'
APP_NAME=TickLab
APP_ENV=testing
APP_DEBUG=true
APP_KEY=$APP_KEY
CI=true

DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASSWORD

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
"

# 5) Exporter variables pour artisan et exécuter commandes
docker compose exec -T app bash -lc "
export APP_ENV=testing
export APP_KEY='$APP_KEY'
export DB_CONNECTION='mysql'
export DB_HOST='$DB_HOST'
export DB_PORT='3306'
export DB_DATABASE='$DB_NAME'
export DB_USERNAME='$DB_USER'
export DB_PASSWORD='$DB_PASSWORD'
export CACHE_DRIVER='array'
export SESSION_DRIVER='array'

# 6) Exécuter migrations
if ! php artisan migrate:fresh --force; then
    echo 'Migrations failed'
    php artisan migrate:status
    exit 1
fi

# 7) Clear cache/config
php artisan config:clear
php artisan cache:clear || true

# 8) Lancer les tests
vendor/bin/phpunit --configuration phpunit.xml --testdox
"

echo "✅ Tests completed successfully."
'''
