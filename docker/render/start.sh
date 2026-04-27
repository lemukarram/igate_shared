#!/bin/bash

# Start PHP-FPM in the background
php-fpm -D

# Replace the PORT variable in Nginx config
sed -i "s/LISTEN_PORT/${PORT:-80}/g" /etc/nginx/conf.d/default.conf

# Start Nginx in the foreground
nginx -g "daemon off;"
