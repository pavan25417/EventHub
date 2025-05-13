# Use official PHP image with Apache
FROM php:8.1-apache

# Install PDO and MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module (useful for Laravel or clean URLs)
RUN a2enmod rewrite

# Set working directory and copy project files
WORKDIR /var/www/html
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
