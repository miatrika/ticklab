echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
    ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
      set -eux
      cd ${env.DEPLOY_PATH}
      docker-compose pull
      docker-compose up -d --build
    '
    """
}

sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 80}"
"""

echo "=== Deploy finished ==="
