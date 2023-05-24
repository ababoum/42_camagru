FROM php:8.0-apache

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y \
    sendmail \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure GD extension
RUN docker-php-ext-configure gd --with-jpeg --with-freetype

# Install GD extension
RUN docker-php-ext-install gd

WORKDIR /var/www/html

COPY website/ ./

# Change owner of the website
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80