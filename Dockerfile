FROM composer AS backFiles
WORKDIR /home/builder
COPY BackEnd .
RUN composer update --prefer-dist --ignore-platform-reqs --optimize-autoloader

########################################################################################

FROM node:8 AS frontFiles
WORKDIR /home/builder
COPY FrontEnd .
RUN yarn install
RUN yarn build:prod

########################################################################################

FROM alpine as SSLGenerator
WORKDIR /home/builder
RUN apk update && apk add openssl && \
    openssl genrsa -des3 -passout pass:x -out server.pass.key 2048 && \
    openssl rsa -passin pass:x -in server.pass.key -out ssl-cert-snakeoil.key && \
    rm server.pass.key && \
    openssl req -new -key ssl-cert-snakeoil.key -out ssl-cert-snakeoil.csr -subj "/C=FR/ST=Here/L=LocalHere/O=OrgName/OU=IT Department/CN=example.com" && \
    openssl x509 -req -days 365 -in ssl-cert-snakeoil.csr -signkey ssl-cert-snakeoil.key -out ssl-cert-snakeoil.pem

########################################################################################

FROM php:7.2-apache
RUN apt-get update && apt-get install zlib1g-dev && \
    docker-php-ext-install zip && \
    a2enmod rewrite



COPY --from=frontFiles /home/builder/dist /var/www/html/
COPY --from=backFiles /home/builder /var/www/html/BackEnd
COPY --from=SSLGenerator /home/builder/ssl-cert-snakeoil.pem /etc/ssl/certs/ssl-cert-snakeoil.pem
COPY --from=SSLGenerator /home/builder/ssl-cert-snakeoil.key /etc/ssl/private/ssl-cert-snakeoil.key

COPY BackEnd/hostMyDocs.ini /usr/local/etc/php/php.ini
COPY entrypoint.sh /usr/local/bin/

RUN mkdir -p /var/www/html/data && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html && \
    ln -s /var/www/html/data /data && \
    chmod +x /usr/local/bin/entrypoint.sh


VOLUME /data

EXPOSE 80
EXPOSE 443

ENTRYPOINT /usr/local/bin/entrypoint.sh
