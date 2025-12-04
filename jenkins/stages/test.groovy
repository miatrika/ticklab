echo "=== STAGE: Run Laravel tests (phpunit) ==="

sh '''
#!/bin/bash
set -eux

# 1. Nettoyer TOUT
echo "Cleaning up previous containers..."
docker compose down -v --remove-orphans --timeout 30 || true

# 2. Supprimer le volume de données pour un démarrage propre
docker volume rm -f ticklab_db_data 2>/dev/null || true

# 3. Démarrer la DB SEULEMENT
echo "Starting MySQL database..."
docker compose up -d db

# 4. Attendre que la DB soit prête AVEC gestion d'erreurs
echo "Waiting for DB to be ready..."
MAX_WAIT=60
WAIT_COUNT=0

while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    # Vérifier d'abord si le conteneur est en cours d'exécution
    CONTAINER_STATE=$(docker inspect -f '{{.State.Status}}' ticklab_db 2>/dev/null || echo "not_found")
    
    if [ "$CONTAINER_STATE" != "running" ]; then
        echo "MySQL container is in state: $CONTAINER_STATE"
        
        if [ "$CONTAINER_STATE" = "restarting" ] || [ "$CONTAINER_STATE" = "exited" ]; then
            echo "Checking MySQL logs for errors:"
            docker logs --tail 50 ticklab_db 2>/dev/null || true
            echo "MySQL failed to start. Exiting."
            exit 1
        fi
        
        sleep 2
        WAIT_COUNT=$((WAIT_COUNT + 1))
        continue
    fi
    
    # Essayer de pinger MySQL
    if docker compose exec -T db mysqladmin ping -h "localhost" --silent 2>/dev/null; then
        echo "✅ MySQL is ready and responsive!"
        break
    fi
    
    echo "Attempt $((WAIT_COUNT + 1))/$MAX_WAIT: MySQL starting..."
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 1))
done

if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
    echo "❌ MySQL failed to start within $((MAX_WAIT * 2)) seconds"
    echo "Last container logs:"
    docker logs --tail 100 ticklab_db 2>/dev/null || true
    exit 1
fi

# 5. Donner un peu plus de temps pour que MySQL soit vraiment prêt
sleep 5

# 6. Lancer les tests Laravel
echo "Running Laravel tests..."
docker compose run --rm -T app sh -c "
    # Copier la configuration de test
    cp .env.testing .env
    
    # Créer les dossiers nécessaires
    mkdir -p storage/framework/cache/data \
            storage/framework/views \
            storage/framework/sessions \
            storage/logs \
            bootstrap/cache
    
    # Permissions
    chmod -R 777 storage bootstrap/cache
    
    # Nettoyer le cache
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    # Exécuter les migrations et tests
    php artisan migrate:fresh --seed --force
    vendor/bin/phpunit --configuration phpunit.xml --testdox --stop-on-failure
"

# 7. Sauvegarder le code de sortie des tests
TEST_EXIT_CODE=$?

# 8. Nettoyer (même en cas d'échec des tests)
echo "Cleaning up after tests..."
docker compose down -v --remove-orphans || true

# 9. Sortir avec le code de sortie des tests
exit $TEST_EXIT_CODE
'''