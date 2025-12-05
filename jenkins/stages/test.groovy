echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# 1. Nettoyer TOUT
echo "🧹 Nettoyage complet..."
docker compose down -v --remove-orphans || true

# 2. Démarrer seulement l'app (pas besoin de MySQL avec SQLite)
echo "🚀 Démarrage de l'application..."
docker compose up -d app

# 3. Attendre le démarrage
echo "⏳ Attente de démarrage..."
sleep 3

# 4. Exécuter les tests avec SQLite
echo "🧪 Exécution des tests avec SQLite..."
docker compose exec -T app bash -c "
    # 1. Créer .env pour les tests avec SQLite
    cat > .env << 'EOF'
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=log
MAIL_MAILER=log
EOF
    
    # 2. Préparer l'environnement
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # 3. Nettoyer le cache
    php artisan config:clear
    php artisan cache:clear
    
    # 4. Créer un fichier SQLite temporaire
    touch database/database.sqlite
    
    # 5. Exécuter les migrations et tests
    php artisan migrate:fresh --seed --force
    vendor/bin/phpunit --configuration phpunit.xml --testdox --stop-on-failure
"

# 5. Capturer le code de sortie
TEST_EXIT=$?

# 6. Nettoyer
echo "🧼 Nettoyage..."
docker compose down -v --remove-orphans || true

# 7. Sortir avec le code de test
exit $TEST_EXIT
'''