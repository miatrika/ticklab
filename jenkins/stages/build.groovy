echo "=== STAGE: Build containers & composer install ==="

sh '''
set -eux

# 1 Build l'image app sans cache
docker-compose -f docker-compose.yml build --no-cache app

# 2 Installer les dépendances PHP via Composer dans un conteneur temporaire
docker-compose run --rm -e CI=true app composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# 3 Exécuter les migrations Laravel
docker-compose run --rm -e CI=true app php artisan migrate --force

# 4 Lancer PHP-FPM en arrière-plan si nécessaire (optionnel)
# docker-compose up -d app
'''

echo "=== Build stage finished ==="
