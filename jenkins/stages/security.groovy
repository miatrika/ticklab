echo "=== STAGE: Security Scans (Trivy & Composer audit) ==="

sh '''
set -eux
# scan directement l’image app
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock aquasec/trivy image --severity CRITICAL,HIGH ticklab_app:latest || true
docker-compose run --rm app composer audit || true
'''
echo "=== Security stage finished ==="
