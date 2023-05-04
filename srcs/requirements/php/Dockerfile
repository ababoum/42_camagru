FROM php:8.0-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN apt-get update && apt-get upgrade -y

WORKDIR /var/www/html

COPY website/ ./

EXPOSE 80