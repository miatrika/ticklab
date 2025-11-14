echo "=== STAGE: Build containers & composer install (cached) ==="

sh '''
set -eux
docker-compose build app
docker-compose run --rm -e CI=true app php -v
echo "✅ Build stage completed successfully."
'''
