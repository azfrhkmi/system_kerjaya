FROM php:8.2-cli

# Install PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copy application files
WORKDIR /app
COPY . /app

# Expose default port
EXPOSE 8000

# Start PHP server listening on Railway dynamic PORT
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8000}"]
