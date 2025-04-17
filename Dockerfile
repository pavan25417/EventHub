# Use the official PHP image with Apache
FROM php:8.1-apache

# Copy everything into the Apache web root
COPY . /var/www/html/

# Set proper file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Enable Apache rewrite module (useful for clean URLs or .htaccess files)
RUN a2enmod rewrite

# Expose port 80 (standard HTTP port)
EXPOSE 80
