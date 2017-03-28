#!/bin/sh

chown -R www-data:www-data /var/www/html/data

if [ "$SHOULD_SECURE" = false ] ; then
    a2dissite default-ssl
fi

apache2-foreground
