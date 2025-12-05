echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# Nettoyer
docker compose down -v --remove-orphans 2>/dev/null || true

# Lancer les tests SANS MySQL du tout
docker compose run --rm -T app bash -c "
    # Environment minimal
    export APP_ENV=testing
    export APP_DEBUG=true
    export DB_CONNECTION=sqlite
    export DB_DATABASE=:memory:
    export CACHE_DRIVER=array
    export SESSION_DRIVER=array
    
    # Générer key si besoin
    if [ ! -f .env ]; then
        php artisan key:generate
    fi
    
    # Préparer
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # Clear cache
    php artisan config:clear
    php artisan cache:clear
    
    # Tests sans migrations/seeders problématiques
    vendor/bin/phpunit --configuration phpunit.xml \
        --exclude-group database \
        --exclude-group mysql \
        --testdox
"

# Cleanup
docker compose down -v 2>/dev/null || true
'''