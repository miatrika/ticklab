// jenkins/stages/test.groovy
echo "=== STAGE: Run Laravel tests (phpunit) ==="

try {
    sh '''
    set -eux
    # Ensure DB up and migrations for test env (use sqlite in memory if preferred)
    docker-compose up -d db
    docker-compose run --rm -T app php artisan migrate --force
    # run tests (works with Laravel 8/9)
    docker-compose run --rm -T app vendor/bin/phpunit --configuration phpunit.xml
    '''
    echo "✅ Tests OK"
} catch (err) {
    echo "❌ Tests failed"
    error("Unit tests failed: ${err}")
}

