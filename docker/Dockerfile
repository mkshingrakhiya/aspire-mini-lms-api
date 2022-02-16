FROM php:8.1-apache

LABEL maintainer="Mayur Shingrakhiya"

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1"
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES="10000"
ENV PHP_OPCACHE_MEMORY_CONSUMPTION="192"
ENV PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

COPY ./apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# update repositories & install required dependencies
RUN apt-get update && apt-get install -y zip unzip sqlite3;

# install composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

# install php extensions
RUN docker-php-ext-install opcache pdo pdo_mysql

# clean-up
RUN apt-get -y autoremove && apt-get clean

RUN a2enmod rewrite

EXPOSE 80
