echo "=== STAGE: Run Laravel tests (phpunit) ==="

try {
    sh '''
    set -eux
    # Démarre uniquement la DB
    docker-compose up -d db

    # Lance migrations + tests dans un conteneur temporaire
    docker-compose run --rm -T -e CI=true app sh -c "
        cp .env.testing .env &&
        php artisan migrate:fresh --seed --force &&
        vendor/bin/phpunit --configuration phpunit.xml
    "
    '''
    echo "✅ Tests OK"
} catch (err) {
    echo "❌ Tests failed"
    error("Unit tests failed: ${err}")
}
