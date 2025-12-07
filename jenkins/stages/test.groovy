echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# Variables
DB_USER="root"
DB_PASSWORD="${DB_PASSWORD:-15182114}"
DB_NAME="ticklab"
DB_HOST="db"
MAX_TRIES=30

# 1) Démarrer DB et app
docker compose up -d db app

# 2) Attendre que MySQL soit prêt depuis le container app
i=0
until docker compose exec -T app bash -c "mysqladmin ping -u$DB_USER -p$DB_PASSWORD -h $DB_HOST > /dev/null 2>&1"; do
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

# 3) Exécuter migrations et tests
docker compose exec -T app bash -lc "
php artisan migrate:fresh --force
vendor/bin/phpunit --configuration phpunit.xml --testdox
"

echo "✅ Tests completed successfully."
'''
