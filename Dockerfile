# ===============================
# 🧩 Étape 1 : Builder les dépendances PHP avec Composer
# ===============================
FROM composer:2 AS vendor

WORKDIR /app

# Copie uniquement les fichiers Composer pour profiter du cache Docker
COPY composer.json composer.lock ./

# ARG pour définir l'environnement
ARG APP_ENV=production

# Installe les dépendances (sans scripts pour éviter artisan)
RUN if [ "$APP_ENV" = "local" ]; then \
        composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    else \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    fi


# ===============================
# ⚙️ Étape 2 : Runtime PHP-FPM
# ===============================
FROM php:8.2-fpm

# Installe les dépendances système et PHP nécessaires à Laravel
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

# ⚡ Ajout : Installer Composer dans l’image finale (solution stable CI/CD)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copie du projet
# Copie uniquement composer.* pour le cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
# Copie le reste des fichiers de l’application
COPY . .

# Copie des dépendances PHP depuis l’étape Composer
COPY --from=vendor /app/vendor ./vendor

# Permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# PHP-FPM écoute sur TCP (port 9000)
RUN echo "listen = 0.0.0.0:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

# Copie et autorisation de l’entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
