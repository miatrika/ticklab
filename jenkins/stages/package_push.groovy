echo "=== 🚀 STAGE: Package & Push Docker images ==="

withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKERHUB_USER', passwordVariable: 'DOCKERHUB_PASS')]) {
    sh '''
    #!/bin/bash
    set -eux

    # Build Docker image
    docker build -t ${REGISTRY}:${IMAGE_TAG} .

    # Tag latest
    docker tag ${REGISTRY}:${IMAGE_TAG} ${REGISTRY}:latest

    # Login to Docker Hub
    echo "$DOCKERHUB_PASS" | docker login -u "$DOCKERHUB_USER" --password-stdin

    # Push images
    docker push ${REGISTRY}:${IMAGE_TAG}
    docker push ${REGISTRY}:latest

    # Logout
    docker logout
    '''
}

echo "✅ Docker images pushed"
