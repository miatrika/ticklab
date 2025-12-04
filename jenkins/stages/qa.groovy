echo "=== STAGE: QA - PHPCS / PHPStan ==="

sh '''
set -eux
docker compose run --rm app ./vendor/bin/phpcs --standard=PSR12 --extensions=php app
docker compose run --rm app ./vendor/bin/phpstan analyse --configuration=phpstan.neon
'''

echo "=== QA stage finished ==="
