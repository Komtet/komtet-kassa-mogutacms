#FROM php:5.6.38-apache
FROM php:7.2-apache
RUN a2enmod rewrite
RUN docker-php-ext-install mysqli

RUN apt-get update && apt-get install -y zlib1g-dev libpng-dev libjpeg62-turbo-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install gd

WORKDIR /var/tmp

RUN curl -fsSL 'http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz' -o ioncube.tar.gz \
    && tar -xvvzf ioncube.tar.gz \
    && cp ioncube/ioncube_loader_lin_7.2* /usr/local/lib/php/extensions/no-debug-non-zts-20170718 \
    && docker-php-ext-enable ioncube_loader_lin_7.2