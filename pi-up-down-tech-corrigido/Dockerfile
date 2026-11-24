FROM php:8.2-apache

RUN a2enmod rewrite \
    && docker-php-ext-install pdo_mysql

WORKDIR /var/www/html

COPY ./src/ /var/www/html/

EXPOSE 80
