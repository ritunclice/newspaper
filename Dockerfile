FROM php:8.2-apache

# Enable Apache mod_rewrite (required for .htaccess URL rewriting)
RUN a2enmod rewrite

# Install mysqli extension (used in db.php)
RUN docker-php-ext-install mysqli

# Allow .htaccess overrides in web root
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Create uploads directory and set correct permissions
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads

EXPOSE 80
