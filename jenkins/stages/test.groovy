echo "=== STAGE: Run Laravel tests (phpunit) ==="

try {
    sh '''
    set -eux
    # Démarre uniquement la DB de test
    docker-compose up -d db

    # Lance migrations + tests dans un conteneur temporaire
    docker-compose run --rm -T -e CI=true -e APP_ENV=testing app sh -c '
        cp .env.testing .env &&
        mkdir -p storage/framework/cache/data storage/framework/views storage/framework/sessions storage/logs &&
        chmod -R 777 storage &&
        php artisan migrate:fresh --seed --force &&
        vendor/bin/phpunit --configuration phpunit.xml
    '
    '''
    echo "✅ Tests OK"
} catch (err) {
    echo "❌ Tests failed"
    error("Unit tests failed: ${err}")
}
