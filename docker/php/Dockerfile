FROM php:8.2-apache

# Una sola capa RUN = menos espacio y build más rápido
RUN a2enmod rewrite && \
    docker-php-ext-install pdo pdo_mysql

RUN sed -i 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html