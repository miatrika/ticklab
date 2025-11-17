ARG APP_IMAGE
FROM ${APP_IMAGE} AS app

FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copie le code complet de l'app
COPY --from=app /var/www/html /var/www/html

# Copie les certificats SSL
COPY nginx/ssl /etc/nginx/ssl

RUN chown -R nginx:nginx /var/www/html
EXPOSE 80 443
