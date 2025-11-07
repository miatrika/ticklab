##############################################
# Étape 1 : Builder l’application Laravel
##############################################
FROM docker.io/miatrika05/ticklab:latest AS app

##############################################
# Étape 2 : Image Nginx
##############################################
FROM nginx:alpine

# Copier la configuration nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copier le dossier public depuis l'étape app
COPY --from=app /var/www/html/public /var/www/html/public

# Ajuster les permissions
RUN chown -R nginx:nginx /var/www/html/public

EXPOSE 80
