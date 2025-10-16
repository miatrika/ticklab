// jenkins/stages/deploy.groovy
echo "=== STAGE: Deploy to remote server via SSH ==="

// using SSH agent credentials configured in Jenkins (id: deploy-ssh)
sshagent(['deploy-ssh']) {
    sh """
    ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
      set -eux
      cd ${env.DEPLOY_PATH}
      git fetch --all
      git reset --hard origin/main
      docker-compose pull || true
      docker-compose down || true
      docker-compose up -d --build
    '
    """
}

// run remote health check (simple curl)
sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -f http://localhost:${env.HOST_HTTP_PORT:-80} || exit 1"
"""
echo "=== Deploy finished ==="

