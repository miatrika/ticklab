echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# 1. Nettoyer les anciens containers
docker compose down -v --remove-orphans 2>/dev/null || true

# 2. Démarrer db et app ENSEMBLE
echo "Démarrage des services..."
docker compose up -d db app

# 3. Attendre BRIÈVEMENT le démarrage
echo "Attente de démarrage (5s)..."
sleep 5

# 4. Exécuter les tests - IMPORTANT: utiliser docker compose exec au lieu de run
echo "Exécution des tests PHPUnit..."
docker compose exec -T app bash -c "
    # Configuration pour les tests
    cat > .env << 'EOF'
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
    
    # Préparer l'environnement
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # Nettoyer et migrer
    php artisan config:clear
    php artisan cache:clear
    php artisan migrate:fresh --seed --force
    
    # Lancer les tests
    vendor/bin/phpunit --configuration phpunit.xml --testdox
"

# 5. Capturer le code de sortie
TEST_EXIT=$?

# 6. Nettoyer
docker compose down -v 2>/dev/null || true

# 7. Sortir avec le code de test
exit $TEST_EXIT
'''