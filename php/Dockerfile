# Obraz bazowy PHP z FPM (do współpracy z Nginx)
FROM php:8.2-fpm-alpine

# Instalacja rozszerzenia PDO dla PostgreSQL
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html
