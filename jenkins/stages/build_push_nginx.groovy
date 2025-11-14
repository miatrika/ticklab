echo "=== STAGE: Build & Push Nginx image ==="

sh """
set -eux
export DOCKER_BUILDKIT=1
docker build -t docker.io/miatrika05/ticklab-nginx:${IMAGE_TAG} \
--build-arg APP_IMAGE=docker.io/miatrika05/ticklab:${IMAGE_TAG} \
-f docker/nginx/Dockerfile .
withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKERHUB_USER', passwordVariable: 'DOCKERHUB_PASS')]) {
    echo \$DOCKERHUB_PASS | docker login -u \$DOCKERHUB_USER --password-stdin
    docker push docker.io/miatrika05/ticklab-nginx:${IMAGE_TAG}
    docker logout
}
"""
echo "✅ Nginx image pushed"
