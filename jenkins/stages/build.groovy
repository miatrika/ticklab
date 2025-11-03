echo "=== STAGE: Build containers & composer install (cached) ==="

sh '''
set -eux

# Build app image avec cache (plus rapide)
docker-compose build app

# Vérifier que l’image s’exécute
docker-compose run --rm -e CI=true app php -v

echo "✅ Build stage completed successfully."
'''
