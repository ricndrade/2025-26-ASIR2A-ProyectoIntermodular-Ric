FROM php:8.2-apache

# Una sola capa RUN = menos espacio y build más rápido
RUN a2enmod rewrite && \
    docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libwebp-dev \
        imagemagick \
        libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN sed -i 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf

COPY php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html