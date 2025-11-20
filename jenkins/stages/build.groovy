
        echo "=== STAGE: Build app ==="

        sh '''
        set -eux

        docker compose down -v || true
        docker compose up -d db
        
        # Build l’image de l’app en utilisant l’ancien docker-compose
        docker compose build app
        until docker compose exec -T db mysqladmin ping -h "127.0.0.1" --silent; do
        echo "Waiting for MySQL..."
        sleep 3
        done

        # Vérifier PHP
        docker compose run --rm -e CI=true app php -v
        '''

        echo "✅ Build stage completed successfully."

