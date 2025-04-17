# Use the official PHP image with Apache
FROM php:8.1-apache

# Copy your PHP files to the Apache server root
COPY . /var/www/html/

# Give the right permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable Apache's rewrite module (optional)
RUN a2enmod rewrite

# Expose port 80 for HTTP
EXPOSE 80
