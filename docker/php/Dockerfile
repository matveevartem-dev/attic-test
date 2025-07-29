FROM php:8.4-fpm

RUN apt update
RUN apt install -y curl zip libzip-dev unzip
RUN apt update && apt -y upgrade

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
install-php-extensions apcu bcmath exif grpc intl opcache pdo pdo_mysql yaml zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1