FROM php:8.2-apache

RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql

COPY php.ini /usr/local/etc/php/conf.d/custom.ini