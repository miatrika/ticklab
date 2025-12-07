stage('Run Laravel tests') {
  steps {
    sh '''
    set -eux

    # 1) Définir mémoire pour PHP/Composer
    export COMPOSER_MEMORY_LIMIT=-1
    export APP_ENV=testing
    export DB_PASSWORD=${DB_PASSWORD:-15182114}

    # 2) Démarrer la DB
    docker compose up -d db

    # 3) Attendre MySQL
    echo "Waiting for MySQL..."
    MAX_TRIES=30
    i=0
    until docker compose exec -T db bash -c "mysqladmin ping -uroot -p$DB_PASSWORD > /dev/null 2>&1"; do
        i=$((i+1))
        if [ "$i" -ge "$MAX_TRIES" ]; then
            echo "MySQL did not become ready in time."
            docker compose ps
            exit 1
        fi
        echo "MySQL not ready yet..."
        sleep 2
    done
    echo "MySQL is ready!"

    # 4) Démarrer App
    docker compose up -d app

    # 5) Créer .env.testing
    docker compose exec -T app bash -lc "cat > .env.testing <<'EOF'
APP_NAME=TickLab
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:ev7dyC9EYuNtHUd0UrEl6m5GFdLkuygJeIIAcL+oBeo=
CI=true

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ticklab
DB_USERNAME=root
DB_PASSWORD=$DB_PASSWORD

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
EOF
    "

    # 6) Exécuter artisan avec limite mémoire
    docker compose exec -T app bash -lc "
export APP_ENV=testing
php -d memory_limit=2G artisan migrate:fresh --force || { echo 'Migrations failed'; php artisan migrate:status; exit 1; }
php artisan config:clear
php artisan cache:clear || true
vendor/bin/phpunit --memory-limit=2048M --configuration phpunit.xml --testdox
    "

    echo "✅ Tests completed successfully."
    '''
  }
}
