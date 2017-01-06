FROM tracesoftware/php:7-apache

COPY FrontEnd/dist /var/www/html
COPY BackEnd/src /var/www/html/BackEnd/src
COPY BackEnd/index.php /var/www/html/BackEnd
COPY BackEnd/.htaccess /var/www/html/BackEnd
COPY BackEnd/vendor /var/www/html/BackEnd/vendor
