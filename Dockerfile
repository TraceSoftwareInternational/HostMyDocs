FROM php:7.1-apache

COPY BackEnd /var/www/html/BackEnd
COPY FrontEnd/dist /var/www/html/
