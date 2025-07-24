#FROM php:5.6.38-apache
FROM php:7.4-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libzip-dev

RUN docker-php-ext-install mysqli zip gd

WORKDIR /var/tmp

RUN curl -fsSL 'http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz' -o ioncube.tar.gz \
    && tar -xvvzf ioncube.tar.gz \
    && cp ioncube/ioncube_loader_lin_7.4* /usr/local/lib/php/extensions/no-debug-non-zts-20190902 \
    && docker-php-ext-enable ioncube_loader_lin_7.4
