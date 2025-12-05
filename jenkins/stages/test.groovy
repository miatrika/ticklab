echo "=== STAGE: Run Laravel tests ==="

sh '''
#!/bin/bash
set -eux

# 1. Nettoyer TOUT
echo "🧹 Nettoyage complet..."
docker compose down -v --remove-orphans || true

# 2. Démarrer MySQL et l'app
echo "🚀 Démarrage des services..."
docker compose up -d db app

# 3. Attendre le démarrage
echo "⏳ Attente de démarrage..."
sleep 5

# 4. Exécuter les tests - OPTION 1: Utiliser MySQL
echo "🧪 Exécution des tests avec MySQL..."
docker compose exec -T app bash -c "
    # 1. Copier .env.testing et CORRIGER les valeurs
    cp .env.testing .env
    
    # 2. CORRECTION: Remplacer db_test par db (nom du service dans docker-compose)
    sed -i 's/DB_HOST=db_test/DB_HOST=db/' .env
    sed -i 's/DB_PASSWORD=root/DB_PASSWORD=15182114/' .env  # Votre mot de passe Docker
    
    # 3. Vérifier/ajouter APP_KEY si manquante
    if ! grep -q '^APP_KEY=' .env || grep -q '^APP_KEY=$' .env; then
        echo 'APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=' >> .env
    fi
    
    # 4. Préparer l'environnement
    mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs
    chmod -R 777 storage
    
    # 5. Nettoyer le cache
    php artisan config:clear
    php artisan cache:clear
    
    # 6. Attendre que MySQL soit prêt (timeout court)
    echo 'Attente de MySQL...'
    for i in {1..10}; do
        if php artisan tinker --execute='try { DB::connection()->getPdo(); echo \"MySQL OK\"; } catch (Exception \$e) { exit(1); }' 2>/dev/null; then
            echo '✅ MySQL connecté'
            break
        fi
        echo 'Tentative $i/10...'
        sleep 2
    done
    
    # 7. Exécuter les migrations et tests
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