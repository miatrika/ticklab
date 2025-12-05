echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# 1. Nettoyer
docker compose down -v --remove-orphans || true

# 2. Démarrer MySQL proprement
docker volume rm -f ticklab_db_data 2>/dev/null || true
docker compose up -d db

# 3. Attendre MySQL MAX 20 secondes
echo "Waiting for MySQL..."
for i in {1..10}; do
    if docker compose exec -T db mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo "MySQL ready after ${i} attempts"
        break
    fi
    echo "Attempt ${i}/10..."
    sleep 2
done

# 4. Exécuter les tests (avec fallback SQLite)
docker compose run --rm -T app sh -c "
    # Utiliser SQLite si MySQL n'est pas disponible
    if ! mysql -h db -uroot -p15182114 -e 'SELECT 1' 2>/dev/null; then
        echo 'Using SQLite instead of MySQL'
        echo 'APP_ENV=testing
        DB_CONNECTION=sqlite
        DB_DATABASE=:memory:' > .env
    else
        cp .env.testing .env
    fi
    
    # Préparation
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # Exécution
    php artisan migrate:fresh --seed --force
    vendor/bin/phpunit --configuration phpunit.xml
"

# 5. Cleanup
docker compose down -v || true
'''