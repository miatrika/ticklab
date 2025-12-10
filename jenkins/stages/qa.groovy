echo "=== STAGE: QA - PHPCBF / PHPCS / PHPStan / Larastan ==="

sh '''
set -eux

# 1. Nettoyer les conteneurs précédents
docker compose down --remove-orphans || true

# 2. Construire l’image avec les outils de développement
docker compose build --build-arg INSTALL_DEV=true app

# 3. Démarrer uniquement le conteneur app (pas besoin de DB pour l'analyse statique)
docker compose up -d app

# 4. Vérification de l'installation des outils
echo "Checking installed tools:"
if ! docker compose exec -T app test -f vendor/bin/phpcs || \
   ! docker compose exec -T app test -f vendor/bin/phpcbf || \
   ! docker compose exec -T app test -f vendor/bin/phpstan ; then

    echo "Tools not found → installing..."
    docker compose exec -T app composer require --dev \
        squizlabs/php_codesniffer \
        phpstan/phpstan \
        nunomaduro/larastan
fi

# 5. Auto-correction du code (PHPCBF)
echo "=== Running PHP Code Beautifier (PHPCBF) ==="
docker compose exec -T app php vendor/bin/phpcbf --standard=PSR12 app || true

# 6. Analyse de qualité du code (PHPCS)
echo "=== Running PHP Code Sniffer ==="
docker compose exec -T app php vendor/bin/phpcs --standard=PSR12 --extensions=php app || echo "PHPCS completed"

# 7. Analyse statique avancée avec PHPStan + Larastan
echo "=== Running PHPStan / Larastan ==="
docker compose exec -T app php vendor/bin/phpstan analyse \
    --configuration=phpstan.neon \
    --memory-limit=2G \
    --no-progress || echo "PHPStan completed"

# 8. Nettoyage final
docker compose down
'''
