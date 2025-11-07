FROM docker.io/miatrika05/ticklab:latest AS app
FROM nginx:alpine

COPY nginx/default.conf /etc/nginx/conf.d/default.conf

# Copie du dossier public depuis l'image app distante
COPY --from=app /var/www/html/public /var/www/html/public

RUN chown -R nginx:nginx /var/www/html/public
EXPOSE 80
