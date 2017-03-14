FROM php:7.1-apache
MAINTAINER Matthieu Vion<mvion@trace-software.com>

RUN apt-get update && apt-get install zlib1g-dev && \
    docker-php-ext-install zip && \
    a2enmod rewrite

COPY FrontEnd/dist /var/www/html/

RUN mkdir -p /var/www/html/data && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html && \
    ln -s /var/www/html/data /data

COPY BackEnd /var/www/html/BackEnd

COPY entrypoint.sh /usr/local/bin/

VOLUME /data

ENTRYPOINT entrypoint.sh
