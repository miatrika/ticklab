FROM nginx:alpine

# Copier la configuration nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Déclarer l'ARG avant COPY --from
ARG APP_IMAGE

# Copier le public depuis l'image app
COPY --from=${APP_IMAGE} /var/www/html/public /var/www/html/public

# Ajuster les permissions
RUN chown -R nginx:nginx /var/www/html/public

EXPOSE 80
