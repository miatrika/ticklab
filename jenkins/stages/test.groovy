echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# 1. Nettoyer
docker compose down -v --remove-orphans || true

# 2. Démarrer
docker compose up -d db app
sleep 10  # Attendre assez longtemps

# 3. Exécuter les tests SIMPLEMENT
docker compose exec -T app bash -c '
    # Configuration simple avec SQLite
    cat > .env << EOF
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
    
    # Préparation
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # Cache
    php artisan config:clear
    php artisan cache:clear
    
    # Tests
    php artisan migrate:fresh --seed --force
    vendor/bin/phpunit --configuration phpunit.xml --testdox
'

# 4. Nettoyer
docker compose down -v || true
'''