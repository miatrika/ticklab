echo "=== 🚀 STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
  withCredentials([string(credentialsId: 'ticklab-db-password', variable: 'DB_PASSWORD')]) {

    sh '''#!/bin/bash
    set -eux

    echo "🚀 Déploiement sur ${DEPLOY_HOST}"

    # === 1️⃣ Préparer les dossiers sur le serveur distant ===
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
      mkdir -p ${DEPLOY_PATH}/nginx
      mkdir -p ${DEPLOY_PATH}/app_code
    "

    # === 2️⃣ Générer la clé APP_KEY localement (sans dépendre du container) ===
    echo "🔑 Génération locale de APP_KEY..."
    APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")

    if [ -z "$APP_KEY" ]; then
      echo "❌ Impossible de générer la clé APP_KEY"
      exit 1
    fi

    # === 3️⃣ Créer le .env complet sur le serveur ===
    echo "⚙️  Création du .env sur le serveur..."
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "cat > ${DEPLOY_PATH}/app_code/.env <<EOF
APP_NAME=TickLab
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=http://localhost:8080

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
EOF"

    echo "✅ .env créé avec succès et APP_KEY ajoutée"

    # === 4️⃣ Copier docker-compose et nginx ===
    scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/docker-compose.yml
    scp -o StrictHostKeyChecking=no nginx/default.conf ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/nginx/default.conf

    # === 5️⃣ Déploiement Docker ===
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
      set -eux
      cd ${DEPLOY_PATH}
      IMAGE_TAG=${BUILD_NUMBER} DB_PASSWORD='${DB_PASSWORD}' docker compose pull
      IMAGE_TAG=${BUILD_NUMBER} DB_PASSWORD='${DB_PASSWORD}' docker compose up -d --remove-orphans
    "
    '''
  }
}

echo "=== ✅ Déploiement terminé avec succès ==="
