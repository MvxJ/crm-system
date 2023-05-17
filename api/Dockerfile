FROM php:8.2-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get update && apt-get install -y git libzip-dev unzip \
    && docker-php-ext-install zip \
    && a2enmod headers && a2enmod rewrite && service apache2 restart\

RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN docker-php-ext-install pdo pdo_mysql

COPY . /var/www/html

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/html/api