# ===============================
# 🧩 Étape 1 : Build vendor
# ===============================
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
ARG INSTALL_DEV=false
RUN if [ "$INSTALL_DEV" = "true" ]; then \
composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
else \
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
fi


# ===============================
# ⚙️ Étape 2 : Runtime PHP-FPM
# ===============================
FROM php:8.2-fpm
ARG INSTALL_DEV=false
RUN apt-get update && apt-get install -y \
git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
libonig-dev libxml2-dev libzip-dev netcat-openbsd \
&& docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
&& apt-get clean && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor


RUN composer dump-autoload --optimize && php artisan package:discover --ansi || true


RUN mkdir -p storage bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache


RUN echo "listen = 0.0.0.0:9000" > /usr/local/etc/php-fpm.d/zz-docker.conf


COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]