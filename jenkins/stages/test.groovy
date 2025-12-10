echo "=== STAGE: Run Laravel tests ==="

sh '''
set -eux

# Variables
DB_USER="root"
DB_PASSWORD="${DB_PASSWORD:-15182114}"
DB_NAME="ticklab"
DB_HOST="db"
MAX_TRIES=30

# 1) Démarrer seulement la DB d'abord
echo "Starting database..."
docker compose up -d db

# 2) Attendre que MySQL soit prêt
echo "Waiting for MySQL..."
i=0
while true; do
    if docker compose exec -T db mysqladmin ping -u$DB_USER -p$DB_PASSWORD --silent 2>/dev/null; then
        echo "MySQL is ready!"
        break
    fi
    
    i=$((i+1))
    if [ "$i" -ge "$MAX_TRIES" ]; then
        echo "MySQL did not become ready in time."
        docker compose logs db
        exit 1
    fi
    echo "MySQL not ready yet... retry #$i"
    sleep 2
done

# 3) Démarrer l'app MAIS avec une commande qui maintient le conteneur en vie
echo "Starting application..."
docker compose run -d --name ticklab_app_test \
  --entrypoint "tail -f /dev/null" \
  app

# Petite pause pour être sûr que le conteneur est stable
sleep 3

# Vérifier que le conteneur est bien en cours d'exécution
if ! docker ps | grep -q ticklab_app_test; then
    echo "Application container failed to start"
    docker logs ticklab_app_test 2>/dev/null || true
    exit 1
fi

# 4) Exécuter migrations et tests dans le conteneur
echo "Running migrations and tests..."
docker exec ticklab_app_test bash -c "
    # Configurer les variables d'environnement
    export DB_USERNAME='$DB_USER'
    export DB_PASSWORD='$DB_PASSWORD'
    export DB_HOST='$DB_HOST'
    export DB_DATABASE='$DB_NAME'
    export APP_ENV=testing
    
    # Créer le fichier .env.testing
    cat > .env.testing << 'EOF'
    APP_NAME=TickLab
    APP_ENV=testing
    APP_DEBUG=true
    APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
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
    
    # Nettoyer les caches
    php artisan config:clear
    php artisan cache:clear
    
    # Exécuter les migrations
    php artisan migrate:fresh --force
    
    echo "📁 Contenu du dossier tests :"
    ls -R tests/ || echo "⚠️ Aucun dossier tests trouvé"

    # Exécuter les tests
    php artisan test -vvv --testdox || vendor/bin/phpunit -vvv --configuration phpunit.xml --testdox
    
"

# 5) Nettoyage
echo "Cleaning up..."
docker stop ticklab_app_test 2>/dev/null || true
docker rm ticklab_app_test 2>/dev/null || true

echo "✅ Tests completed successfully."
'''