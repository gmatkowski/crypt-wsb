FROM php:8.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

#RUN docker-php-ext-install opcache

RUN docker-php-ext-install bcmath

RUN apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN docker-php-ext-install exif && docker-php-ext-enable exif

# Zip
RUN apt-get install -y libzip-dev zip && docker-php-ext-configure zip && docker-php-ext-install zip

#RUN mv "./php.ini" "$PHP_INI_DIR/php.ini"

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www
