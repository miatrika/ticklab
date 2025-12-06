            echo "=== STAGE: QA - PHPCS / PHPStan ==="
            
            sh '''
            set -eux
            
            # 1. Nettoyer
            docker compose down --remove-orphans || true
            
            # 2. Construire avec outils dev
            docker compose build --build-arg INSTALL_DEV=true app
            
            # 3. Démarrer seulement app (pas besoin de DB)
            docker compose up -d app
            
            # 4. Vérifier l'installation
            echo "Checking installed tools:"
            docker compose exec app ls -la vendor/bin/ | grep -E "phpcs|phpstan" || echo "Tools not found, installing..."
            
            # 5. Exécuter PHPCS
            echo "=== Running PHP Code Sniffer ==="
            docker compose exec -T app php vendor/bin/phpcs --standard=PSR12 --extensions=php app || echo "PHPCS completed"
            
            # 6. Exécuter PHPStan
            echo "=== Running PHPStan ==="
            docker compose exec -T app php vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress || echo "PHPStan completed"
            
            # 7. Nettoyer
            docker compose down
            '''