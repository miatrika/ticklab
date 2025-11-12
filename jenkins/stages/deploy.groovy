 echo "=== 🚀 STAGE: Deploy to remote server ==="

  sshagent(['deploy-ssh']) {
    withCredentials([string(credentialsId: 'ticklab-db-pass', variable: 'DB_PASSWORD')]) {

      sh """
        echo "🚀 Déploiement sur ${env.DEPLOY_HOST}"

        # === 1️⃣ Préparer les dossiers sur le serveur distant ===
        ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
           mkdir -p ${env.DEPLOY_PATH}/nginx
           mkdir -p ${env.DEPLOY_PATH}/app_code
        '

        # === 2️⃣ Générer le fichier .env.deploy avec secrets ===
        echo "⚙️  Génération du .env.deploy sur le serveur..."
        ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
           cat > ${env.DEPLOY_PATH}/.env.deploy <<EOF
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
DB_PASSWORD=${DB_PASSWORD}

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
EOF
        echo "✅ .env.deploy créé avec succès"
        '

        # === 3️⃣ Copier les fichiers nécessaires ===
        echo "📦 Copie des fichiers docker-compose et nginx..."
        scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/docker-compose.yml
        scp -o StrictHostKeyChecking=no nginx/default.conf ${env.DEPLOY_USER}@${env.DEPLOY_HOST}:${env.DEPLOY_PATH}/nginx/default.conf

        # === 4️⃣ Déploiement Docker ===
        ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
           set -eux
           cd ${env.DEPLOY_PATH}
           mv -f .env.deploy app_code/.env.prod
           IMAGE_TAG=${env.BUILD_NUMBER} docker compose pull
           IMAGE_TAG=${env.BUILD_NUMBER} docker compose up -d --remove-orphans
        '

        # === 5️⃣ Générer APP_KEY si manquante ===
        echo "🔑 Vérification de la clé APP_KEY..."
        ssh -o StrictHostKeyChecking=no ${env.DEPLOY_USER}@${env.DEPLOY_HOST} '
           if ! docker exec ticklab_app php artisan env | grep -q "APP_KEY=base64:"; then
              docker exec ticklab_app php artisan key:generate --force
              echo "✅ Nouvelle clé Laravel générée"
           else
              echo "ℹ️  APP_KEY déjà existante"
           fi
        '
      """
    } // end withCredentials
  } // end sshagent
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

echo "=== ✅ Déploiement terminé avec succès ==="
