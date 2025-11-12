echo "=== STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
    sh """
      echo "🚀 Déploiement sur ${env.DEPLOY_HOST}"

      # === 1️⃣ Préparer les dossiers sur le serveur distant ===
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         mkdir -p ${env.DEPLOY_PATH}/nginx
         mkdir -p ${env.DEPLOY_PATH}/app_code
         mkdir -p ${env.DEPLOY_PATH}
      '

      # === 2️⃣ Générer le fichier .env.prod dynamique ===
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         cat > ${env.DEPLOY_PATH}/.env.prod <<EOF
APP_NAME=TickLab
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:${env.HOST_HTTP_PORT ?: 8080}

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ticklab
DB_USERNAME=root
DB_PASSWORD=${env.DB_PASSWORD}

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
EOF
         echo "✅ .env.prod généré avec succès"
      '

      # === 3️⃣ Copier les fichiers nécessaires ===
      scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml
      scp -o StrictHostKeyChecking=no nginx/default.conf ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/nginx/default.conf

      # === 4️⃣ Lancer le déploiement Docker ===
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         set -eux
         cd ${env.DEPLOY_PATH}
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose pull
         IMAGE_TAG=${env.BUILD_NUMBER} docker compose up -d --remove-orphans
      '

      # === 5️⃣ Générer la clé APP Laravel ===
      ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
         docker exec ticklab_app php artisan key:generate --force
         echo "✅ Clé Laravel générée avec succès"
      '
    """
}

echo "=== 🔍 Vérification du déploiement ==="
sh """
ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
  echo "Test HTTP sur http://localhost:${env.HOST_HTTP_PORT ?: 8080}"
  if curl -fs http://localhost:${env.HOST_HTTP_PORT ?: 8080} > /dev/null; then
    echo "✅ Application TickLab déployée avec succès"
  else
    echo "❌ L’application ne répond pas"
    exit 1
  fi
'
"""

echo "=== ✅ Deploy finished ==="
