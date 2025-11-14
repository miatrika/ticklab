echo "=== 🚀 STAGE: Deploy to remote server ==="

sshagent(['deploy-ssh']) {
  withCredentials([string(credentialsId: 'ticklab-db-password', variable: 'DB_PASSWORD')]) {
    sh """
    set -eux
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
      mkdir -p ${DEPLOY_PATH}/nginx
      mkdir -p ${DEPLOY_PATH}/app_code
    "
    scp -r $WORKSPACE/* ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/app_code/
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "cat > ${DEPLOY_PATH}/app_code/.env <<EOF
APP_NAME=TickLab
APP_ENV=production
APP_KEY=base64:E4fqEzMmJBOQeHr7Z10WmUKwds+obTfE+cHJxPOnOPs=
APP_DEBUG=false
APP_URL=http://192.168.100.50:8080

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
SESSION_LIFETIME=120
SESSION_DOMAIN=192.168.100.50
QUEUE_CONNECTION=sync
EOF
    "
    scp -o StrictHostKeyChecking=no docker-compose.prod.yml ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/docker-compose.yml
    scp -o StrictHostKeyChecking=no nginx/default.conf ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/nginx/default.conf
    ssh -o StrictHostKeyChecking=no ${DEPLOY_USER}@${DEPLOY_HOST} "
      cd ${DEPLOY_PATH}
      IMAGE_TAG=${BUILD_NUMBER} DB_PASSWORD='${DB_PASSWORD}' docker-compose pull
      IMAGE_TAG=${BUILD_NUMBER} DB_PASSWORD='${DB_PASSWORD}' docker-compose up -d --remove-orphans
    "
    """
  }
}
echo "✅ Deployment completed successfully"
