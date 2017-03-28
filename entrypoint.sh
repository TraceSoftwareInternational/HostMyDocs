#!/bin/sh

chown -R www-data:www-data /var/www/html/data

if [ -z "$SHOULD_SECURE" ] ; then
    a2enmod ssl
    a2ensite default-ssl
fi

apache2-foreground
