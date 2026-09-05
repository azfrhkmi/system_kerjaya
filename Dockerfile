FROM php:8.2-cli

# Install PDO MySQL & PDO SQLite extensions
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mysqli

# Copy application files
WORKDIR /app
COPY . /app

# Expose default port
EXPOSE 8000

# Start PHP server listening on Railway dynamic PORT
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8000}"]
