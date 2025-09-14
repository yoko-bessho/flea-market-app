FROM php:8.0-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update && apt-get install -y     git     curl     libpng-dev     libonig-dev     libxml2-dev     zip     unzip     default-mysql-client     libjpeg62-turbo-dev

RUN docker-php-ext-configure gd --with-jpeg && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www