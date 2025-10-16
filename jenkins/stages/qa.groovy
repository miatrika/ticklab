// jenkins/stages/qa.groovy
echo "=== STAGE: QA - PHPCS / PHPStan / (Sonar) ==="

sh '''
set -eux
# PHPCS (style)
docker-compose run --rm app vendor/bin/phpcs --standard=PSR12 --extensions=php app || true

# PHPStan (static analysis) - configuration file phpstan.neon expected at repo root
docker-compose run --rm app vendor/bin/phpstan analyse --configuration=phpstan.neon || true

# optional: SonarQube analysis (if Sonar server available)
# docker run --rm -v $(pwd):/usr/src sonarsource/sonar-scanner-cli \
#   -Dsonar.projectKey=ticklab -Dsonar.sources=app,resources -Dsonar.host.url=http://sonar:9000 -Dsonar.login=$SONAR_TOKEN || true
'''
echo "=== QA stage finished (reports above) ==="

