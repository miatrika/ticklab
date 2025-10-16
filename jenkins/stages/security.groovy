// jenkins/stages/security.groovy
echo "=== STAGE: Security Scans (Trivy) ==="

sh '''
set -eux
# build a temporary image for scanning
docker build -t ticklab_tmp_scan:latest .
# run trivy (requires trivy installed on Jenkins agent or run via container)
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock aquasec/trivy image --severity CRITICAL,HIGH ticklab_tmp_scan:latest || true

# composer audit (requires composer 2 audit plugin)
docker-compose run --rm app composer audit || true
'''
echo "=== Security stage finished ==="

