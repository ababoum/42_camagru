FROM php:8.0-apache

# Install necessary extensions and packages
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    sendmail \
    libjpeg62-turbo-dev \
    libpng-dev \
    libfreetype6-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install and configure GD extension
RUN docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install gd

# Install PDO and PostgreSQL extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Explicitly enable the pdo_pgsql extension
RUN docker-php-ext-enable pdo_pgsql

WORKDIR /var/www/html

# Copy Apache configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache modules
RUN a2enmod rewrite

# Enable PHP error logging
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/php.ini

# Create the PHP error log file and set permissions
RUN touch /var/log/php_errors.log && \
    chown www-data:www-data /var/log/php_errors.log && \
    chmod 664 /var/log/php_errors.log

EXPOSE 80
