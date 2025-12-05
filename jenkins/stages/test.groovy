echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# Nettoyer
docker compose down -v --remove-orphans 2>/dev/null || true

# Lancer les tests avec configuration complète
docker compose run --rm -T app bash -c "
    # Configuration complète
    cat > .env << 'EOF'
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
APP_ENV=testing
APP_DEBUG=true
CI=true
# SQLite avec fichier (pas :memory: pour que les migrations fonctionnent)
DB_CONNECTION=sqlite
DB_DATABASE=database/testing.sqlite
# Désactiver MySQL
DB_HOST=127.0.0.1
DB_PORT=3306
# Drivers mémoire
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
    
    # Créer le répertoire et fichier de base de données
    mkdir -p database
    touch database/testing.sqlite
    chmod 666 database/testing.sqlite
    
    # Préparer
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # Clear
    php artisan config:clear
    php artisan cache:clear
    
    # Migrations (l'entrypoint va les exécuter car SKIP_MIGRATIONS=false pour SQLite)
    echo 'Migrations should run via entrypoint...'
    
    # Si les migrations ne tournent pas via l'entrypoint, les forcer:
    php artisan migrate:fresh --force
    
    # Tests
    vendor/bin/phpunit --configuration phpunit.xml --testdox --stop-on-failure
"

# Cleanup
docker compose down -v 2>/dev/null || true
'''