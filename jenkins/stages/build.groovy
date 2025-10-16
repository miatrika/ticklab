// jenkins/stages/build.groovy
echo "=== STAGE: Build containers & composer install ==="

// ensure environment variables exist
sh '''
set -eux
# build containers (no cache on CI to ensure fresh deps)
docker-compose -f docker-compose.yml build --no-cache app
# install composer deps inside container (vendor volume persists)
docker-compose run --rm app composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
'''
echo "=== Build stage finished ==="

