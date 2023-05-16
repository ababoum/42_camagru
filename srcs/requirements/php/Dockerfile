FROM php:8.0-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y \
    sendmail \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY website/ ./

EXPOSE 80