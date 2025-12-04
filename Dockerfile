# ===============================
# 🧩 Étape 1 : Builder les dépendances PHP avec Composer
# ===============================
FROM composer:2 AS vendor

WORKDIR /app

# Copie uniquement les fichiers Composer pour profiter du cache Docker
COPY composer.json composer.lock ./

# ARG pour définir l'environnement (par défaut = production)
ARG APP_ENV=production
ARG INSTALL_DEV=false

RUN if [ "$INSTALL_DEV" = "true" ]; then \
        composer install --no-interaction --prefer-dist --optimize-autoloader; \
    else \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader; \
    fi

# ===============================
# ⚙️ Étape 2 : Runtime PHP-FPM
# ===============================
FROM php:8.2-fpm

# Installe les dépendances système et extensions PHP nécessaires à Laravel
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

# ⚡ Ajout : Installer Composer dans l’image finale (utile en CI/CD)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copie du code source (tout le projet)
COPY . .

# Copie du dossier vendor depuis l’étape précédente
COPY --from=vendor /app/vendor ./vendor

# Permissions nécessaires à Laravel
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Configuration PHP-FPM pour écouter sur le port 9000
RUN echo "listen = 0.0.0.0:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf

# Copie et autorisation de l’entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
