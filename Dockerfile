# Dockerfile untuk Deployment di Railway / Render
FROM php:8.2-apache

# Install PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache Mod Rewrite
RUN a2enmod rewrite

# Konfigurasi Apache untuk mendengar pada port dynamic Railway
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Salin fail projek
COPY . /var/www/html/

# Tetapkan kebenaran folder
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
