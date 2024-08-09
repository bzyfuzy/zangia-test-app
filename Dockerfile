# Use the official PHP image as the base image
FROM php:8.2-fpm
# Set working directory
WORKDIR /var/www/html

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql