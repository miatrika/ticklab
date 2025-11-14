echo "=== STAGE: Security Scans (Trivy & Composer audit) ==="

sh '''
set -eux
docker run --rm \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v $HOME/.cache/trivy:/root/.cache/trivy \
  aquasec/trivy image \
  --severity CRITICAL,HIGH \
  --ignore-unfixed \
  ticklab_app:latest || true

docker compose run --rm app composer audit || true
'''
echo "=== Security stage finished ==="
