
        echo "=== STAGE: Build app ==="

        sh '''
        set -eux
        # Build l’image de l’app en utilisant l’ancien docker-compose
        docker compose build --no-cache app

        docker compose run --rm app composer update --lock 
        # Installer les dépendances (respecte maintenant le lock file mis à jour) 
        docker compose run --rm -e CI=true app composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

        # Vérifier PHP
        docker compose run --rm -e CI=true app php -v
        '''

        echo "✅ Build stage completed successfully."

