#!/bin/bash

usermod -l www www-data
groupmod -n www www-data

rm /etc/nginx/sites-available/default
rm /etc/nginx/sites-enabled/default
rm /etc/nginx/nginx.conf
rm /usr/local/etc/php-fpm.conf

ln -s /app/nginx.conf /etc/nginx/nginx.conf
ln -s /app/php-fpm.conf /usr/local/etc/php-fpm.conf

php-fpm
nginx

sleep 10s
export LC_ALL=C
PGPASSWORD=postgres psql --username postgres --host postgres < /app/schema.sql

code-server --host 0.0.0.0 --port 53586 --auth none
