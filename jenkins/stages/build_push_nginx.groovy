echo "=== 🚀 STAGE: Build & Push Nginx image ==="

withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DOCKERHUB_USER', passwordVariable: 'DOCKERHUB_PASS')]) {
    sh '''
    #!/bin/bash
    set -eux
    export DOCKER_BUILDKIT=1

    # Build Nginx image
    docker build -t docker.io/miatrika05/ticklab-nginx:${IMAGE_TAG} \
        --build-arg APP_IMAGE=docker.io/miatrika05/ticklab:${IMAGE_TAG} \
        -f docker/nginx/Dockerfile .

    # Login to Docker Hub
    echo "$DOCKERHUB_PASS" | docker login -u "$DOCKERHUB_USER" --password-stdin

    # Push Nginx image
    docker push docker.io/miatrika05/ticklab-nginx:${IMAGE_TAG}
    docker push docker.io/miatrika05/ticklab-nginx:latest

    # Logout
    docker logout
    '''
}

echo "✅ Nginx image pushed"
