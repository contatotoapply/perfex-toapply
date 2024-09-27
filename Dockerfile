FROM php:8.3-apache

WORKDIR /var/www/html

COPY php.ini /usr/local/etc/php/conf.d

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libicu-dev \
    libxslt-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libonig-dev \
    libbz2-dev \
    libmcrypt-dev \
    libpng-dev \
    libgd-dev \
    libpq-dev \
    libxslt1-dev \
    libc-client-dev \
    libkrb5-dev 
    
RUN docker-php-ext-configure \
    imap --with-kerberos --with-imap-ssl

RUN docker-php-ext-install \
    mysqli \
    imap \
    gd \
    zip \
    pdo_mysql

EXPOSE 8080 3306