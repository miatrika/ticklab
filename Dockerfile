# ===============================
# 🧩 Étape 1 : Builder les dépendances PHP avec Composer
# ===============================
FROM composer:2 AS vendor

WORKDIR /app

# Copie uniquement les fichiers Composer pour profiter du cache Docker
COPY composer.json composer.lock ./

# ARG pour définir l'environnement
ARG APP_ENV=production

# Installe les dépendances sans exécuter les scripts pour éviter l'erreur artisan
RUN if [ "$APP_ENV" = "local" ]; then \
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    else \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    fi

# ===============================
# ⚙️ Étape 2 : Runtime PHP-FPM
# ===============================
FROM php:8.2-fpm

# Installe les extensions nécessaires à Laravel + netcat pour entrypoint
# Installe les extensions nécessaires à Laravel + netcat-openbsd pour entrypoint
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Dossier de travail
WORKDIR /var/www/html

# Copie tout le projet Laravel
COPY . .

# Copie les dépendances PHP depuis l’étape Composer
COPY --from=vendor /app/vendor ./vendor

# Création des dossiers et permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Forcer PHP-FPM à écouter sur TCP 9000 pour Nginx
RUN echo "listen = 0.0.0.0:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

# Copie de l’entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose le port PHP-FPM
EXPOSE 9000

# Utilisation de l’entrypoint pour automatiser les migrations et sessions
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
