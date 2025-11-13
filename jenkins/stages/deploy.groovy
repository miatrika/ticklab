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

    # === 2️⃣ Copier le code Laravel complet ===
    echo "📦 Copie du code Laravel complet..."
    # ⚠️ Utilisation de $WORKSPACE pour pointer correctement vers le code
    scp -r $WORKSPACE/app_code ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/

    # === 3️⃣ Créer le .env sur le serveur ===
    echo "⚙️  Création du .env sur le serveur..."
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "cat > ${DEPLOY_PATH}/app_code/.env <<EOF
APP_NAME=TickLab
APP_ENV=production
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

    echo "✅ .env créé avec succès"

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

    # === 6️⃣ Vérification de APP_KEY ===
    echo "🔑 Vérification de la clé APP_KEY..."
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
      set -eux
      ENV_FILE='${DEPLOY_PATH}/app_code/.env'
      if ! grep -q 'APP_KEY=' \"\$ENV_FILE\"; then
          echo '⚙️  APP_KEY manuelle à insérer nécessaire'
      else
          echo 'ℹ️  APP_KEY déjà présente dans .env'
      fi
    "
    '''
  }
}

echo "=== ✅ Déploiement terminé avec succès ==="
