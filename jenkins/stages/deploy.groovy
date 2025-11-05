echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
      # Crée les dossiers sur le serveur
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         mkdir -p ${env.DEPLOY_PATH}/nginx
      '

      # Copie docker-compose et default.conf
      scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml
      scp -o StrictHostKeyChecking=no nginx/default.conf ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/nginx/default.conf

      # Lancer les conteneurs
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         set -eux
         cd ${env.DEPLOY_PATH}
         IMAGE_TAG=${env.BUILD_NUMBER} docker-compose pull
         IMAGE_TAG=${env.BUILD_NUMBER} docker-compose up -d --remove-orphans
      '
    """
}
echo""
sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 80}"
"""

echo "=== Deploy finished ==="
