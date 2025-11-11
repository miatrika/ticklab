echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
      # Préparation des dossiers sur le serveur
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         sudo mkdir -p ${env.DEPLOY_PATH}/nginx
         sudo mkdir -p ${env.DEPLOY_PATH}/app_code
         sudo chown -R ${env.DEPLOY_USER}:${env.DEPLOY_USER} ${env.DEPLOY_PATH}
      '

      # Copie des fichiers nécessaires
      scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml
      scp -o StrictHostKeyChecking=no nginx/default.conf ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/nginx/default.conf
      scp -o StrictHostKeyChecking=no .env.prod ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:/tmp/.env

      # Déplacement du fichier .env dans le bon répertoire (avec sudo)
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         mv /tmp/.env ${env.DEPLOY_PATH}/app_code/.env
      '

      # Lancement du déploiement
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         set -eux
         cd ${env.DEPLOY_PATH}
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose pull
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose up -d --remove-orphans
      '
    """
}

echo "=== Deploy finished ==="
echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
      # Préparation des dossiers sur le serveur
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         mkdir -p ${env.DEPLOY_PATH}/nginx
         mkdir -p ${env.DEPLOY_PATH}/app_code
         chown -R ${env.DEPLOY_USER}:${env.DEPLOY_USER} ${env.DEPLOY_PATH}
      '

      # Copie des fichiers nécessaires
      scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml
      scp -o StrictHostKeyChecking=no nginx/default.conf ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/nginx/default.conf
      scp -o StrictHostKeyChecking=no .env.prod ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:/tmp/.env

      # Déplacement du fichier .env dans le bon répertoire (avec sudo)
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         sudo mv /tmp/.env ${env.DEPLOY_PATH}/app_code/.env
      '

      # Lancement du déploiement
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         set -eux
         cd ${env.DEPLOY_PATH}
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose pull
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose up -d --remove-orphans
      '
    """
}

echo ""
sh """
echo "=== Vérification du déploiement ==="
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 8080}"
"""

echo "=== Deploy finished ==="
