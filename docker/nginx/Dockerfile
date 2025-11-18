ARG APP_IMAGE
FROM ${APP_IMAGE} AS app

FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copie SSL dans le conteneur
COPY nginx/ssl /etc/nginx/ssl

# Copie du code complet de l'app
COPY --from=app /var/www/html /var/www/html

RUN chown -R nginx:nginx /var/www/html

EXPOSE 443
