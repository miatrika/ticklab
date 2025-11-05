echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
    # Créer un dossier temporaire sur le serveur pour le déploiement
    ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
      mkdir -p ${env.DEPLOY_PATH}
    '

    # Copier le docker-compose.yml depuis Jenkins vers le serveur
    scp -o StrictHostKeyChecking=no docker-compose.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml

    # Lancer docker-compose sur le serveur pour pull les images et démarrer les conteneurs
    ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
      set -eux
      cd ${env.DEPLOY_PATH}
      docker-compose pull       # récupérer les images déjà construites
      docker-compose up -d      # démarrer les conteneurs
    '
    """
}

sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} "curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 80}"
"""

echo "=== Deploy finished ==="
