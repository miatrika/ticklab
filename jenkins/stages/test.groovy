echo "=== STAGE: Run Laravel tests (phpunit) ==="

try {
    sh '''
    set -eux
    # Démarre uniquement la DB
    docker-compose up -d db

        # Lance migrations + tests dans **un seul run** pour éviter de relancer l'entrypoint
    docker-compose run --rm -T -e CI=true app sh -c "
    composer install --no-interaction --prefer-dist &&
    php artisan key:generate --ansi &&
    php artisan migrate --force &&
    vendor/bin/phpunit --configuration phpunit.xml
    "


    '''
    echo "✅ Tests OK"
} catch (err) {
    echo "❌ Tests failed"
    error("Unit tests failed: ${err}")
}

