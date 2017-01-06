FROM tracesoftware/php:7-apache

COPY BackEnd /var/www/html/BackEnd
COPY FrontEnd/dist /var/www/html/
