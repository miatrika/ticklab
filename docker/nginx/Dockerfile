# docker/nginx/Dockerfile
FROM nginx:alpine

# Copier la configuration nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier le dossier public depuis l'image app
ARG APP_IMAGE
COPY --from=${APP_IMAGE} /var/www/html/public /var/www/html/public

# Ajuster les permissions
RUN chown -R nginx:nginx /var/www/html/public

EXPOSE 80
