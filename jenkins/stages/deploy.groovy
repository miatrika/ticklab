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

echo "=== Vérification du déploiement ==="
sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
  echo "Test HTTP sur http://localhost:${env.HOST_HTTP_PORT ?: 8080}"
  if curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 8080} > /dev/null; then
    echo "✅ Application déployée avec succès."
  else
    echo "❌ L’application ne répond pas."
    exit 1
  fi
'
"""

echo "=== Deploy finished ==="
