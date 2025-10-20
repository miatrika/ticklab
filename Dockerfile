# ===============================
# 🧩 Étape 1 : Builder les dépendances PHP avec Composer
# ===============================
FROM composer:2 AS vendor

WORKDIR /app

# Copie uniquement les fichiers Composer pour profiter du cache Docker
COPY composer.json composer.lock ./

# Installe TOUTES les dépendances (prod + dev)
# car "laravel/pail" est dans require-dev
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# ===============================
# ⚙️ Étape 2 : Runtime PHP-FPM
# ===============================
FROM php:8.2-fpm

# Installe les extensions nécessaires à Laravel
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
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Dossier de travail dans le conteneur
WORKDIR /var/www/html

# Copie le projet Laravel
COPY . .

# Copie les dépendances PHP depuis l’étape Composer
COPY --from=vendor /app/vendor ./vendor

# Assure-toi que le dossier storage et cache soient accessibles à PHP
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose le port PHP-FPM
EXPOSE 9000

# Lancement du serveur PHP-FPM
CMD ["php-fpm"]
