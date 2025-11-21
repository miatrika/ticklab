
        echo "=== STAGE: Build app ==="

        sh '''
        set -eux
        # Build l’image de l’app en utilisant l’ancien docker-compose
        docker compose build --no-cache app

        # Vérifier PHP
        docker compose run --rm -e CI=true app php -v
        '''

        echo "✅ Build stage completed successfully."

