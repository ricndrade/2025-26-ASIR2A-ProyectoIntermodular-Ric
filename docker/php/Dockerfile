FROM php:8.2-apache

RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

COPY php.ini /usr/local/etc/php/conf.d/custom.ini