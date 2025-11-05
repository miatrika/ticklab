echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']){
    sh """
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         mkdir -p ${env.DEPLOY_PATH}
      '
      scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml

       ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         set -eux
         cd ${env.DEPLOY_PATH}
        IMAGE_TAG=${env.BUILD_NUMBER} docker-compose pull
        IMAGE_TAG=${env.BUILD_NUMBER} docker-compose up -d
       '
      """
  }
echo
sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 80}"
"""

echo "=== Deploy finished ==="
