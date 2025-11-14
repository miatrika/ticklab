echo "=== 🚀 STAGE: Package & Push Docker images ==="

sh """
set -eux
docker build -t ${REGISTRY}:${IMAGE_TAG} .
docker tag ${REGISTRY}:${IMAGE_TAG} ${REGISTRY}:latest
withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKERHUB_USER', passwordVariable: 'DOCKERHUB_PASS')]) {
    echo \$DOCKERHUB_PASS | docker login -u \$DOCKERHUB_USER --password-stdin
    docker push ${REGISTRY}:${IMAGE_TAG}
    docker push ${REGISTRY}:latest
    docker logout
}
"""
echo "✅ Docker images pushed"
