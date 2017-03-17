FROM php:7.0-cli

RUN apt-get update && \
    apt-get install -y git zip && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo pdo_mysql

RUN echo "pcre.jit=0" >> /usr/local/etc/php/php.ini
