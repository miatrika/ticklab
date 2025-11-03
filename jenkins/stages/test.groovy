echo "=== STAGE: Run Laravel tests (phpunit) ==="

try {
    sh '''
    set -eux
    docker-compose up -d db
    docker-compose run --rm -T -e CI=true app sh -c "
      php artisan key:generate --ansi &&
      php artisan migrate:fresh --seed --force &&
      vendor/bin/phpunit --configuration phpunit.xml
    "
    '''
    echo "✅ Tests passed"
} catch (err) {
    echo "❌ Tests failed"
    error("Unit tests failed: ${err}")
} finally {
    sh "docker-compose down -v"
}
