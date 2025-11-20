echo "=== STAGE: Ensure DB ==="
sh '''
set -eux
# Crée les volumes si absents
docker volume create ticklab_db_data 2>/dev/null || true
docker volume create ticklab_vendor_data 2>/dev/null || true

            # Démarre MySQL si pas déjà lancé
            if [ -z "$(docker ps -q -f name=ticklab_db)" ]; then
                docker compose up -d db
            fi

            # Attendre que MySQL soit prêt
            until docker compose exec -T db sh -c "mysqladmin ping -h 127.0.0.1 --silent"; do
                echo "Waiting for MySQL..."
                sleep 3
            done
            '''
            echo "✅ DB is ready."