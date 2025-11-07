# Étape 1 : définir l’image source de Nginx
FROM nginx:alpine

# Étape 2 : accepter l’argument
ARG APP_IMAGE

# Étape 3 : importer depuis l’image app (nom complet)
COPY --from=${APP_IMAGE} /var/www/html/public /var/www/html/public

RUN rm /etc/nginx/conf.d/default.conf
# Étape 4 : copier la config nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

RUN chown -R nginx:nginx /var/www/html/public

EXPOSE 80
