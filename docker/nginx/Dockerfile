# Étape 1 : importer l’image app
ARG APP_IMAGE
FROM ${APP_IMAGE} AS app

# Étape 2 : image nginx finale
FROM nginx:alpine

# Supprimer le default.conf de base (pour éviter read-only warning)
RUN rm /etc/nginx/conf.d/default.conf

# Copier ta configuration Nginx personnalisée
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier le dossier public depuis l'image app
COPY --from=app /var/www/html/public /var/www/html/public

RUN chown -R nginx:nginx /var/www/html/public

EXPOSE 80
