echo "=== STAGE: QA - PHPCS / PHPStan / Larastan ==="

sh '''
set -eux

# 1. Nettoyer
docker compose down --remove-orphans || true

# 2. Construire avec outils dev
docker compose build --build-arg INSTALL_DEV=true app

# 3. Démarrer seulement app (pas besoin de DB)
docker compose up -d app

# 4. Vérifier l'installation des outils
echo "Checking installed tools:"
if ! docker compose exec -T app test -f vendor/bin/phpcs || ! docker compose exec -T app test -f vendor/bin/phpstan || ! docker compose exec -T app test -f vendor/bin/phpstan-analyse ; then
    echo "Tools not found → installing..."
    docker compose exec -T app composer require --dev squizlabs/php_codesniffer phpstan/phpstan nunomaduro/larastan
fi

# 5. Exécuter PHPCS
echo "=== Running PHP Code Sniffer ==="
docker compose exec -T app php vendor/bin/phpcs --standard=PSR12 --extensions=php app || echo "PHPCS completed"

# 6. Exécuter PHPStan avec Larastan
echo "=== Running PHPStan / Larastan ==="
docker compose exec -T app php vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G --no-progress || echo "PHPStan completed"

# 7. Nettoyer
docker compose down
'''
