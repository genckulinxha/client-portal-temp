FROM php:8.3-cli-alpine

# Install basic dependencies and required extensions
RUN apk add --no-cache \
    git curl sqlite-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite intl zip exif gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create necessary directories first
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set proper permissions
RUN chmod -R 775 storage bootstrap/cache

# Create basic .env file
RUN echo 'APP_NAME="Client Portal"' > .env && \
    echo 'APP_ENV=local' >> .env && \
    echo 'APP_KEY=' >> .env && \
    echo 'APP_DEBUG=true' >> .env && \
    echo 'APP_URL=http://localhost:8001' >> .env && \
    echo 'LOG_CHANNEL=stack' >> .env && \
    echo 'DB_CONNECTION=sqlite' >> .env && \
    echo 'DB_DATABASE=database/database.sqlite' >> .env && \
    echo 'CACHE_DRIVER=file' >> .env && \
    echo 'SESSION_DRIVER=file' >> .env

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy the rest of the app
COPY . .

# Make startup script executable
RUN chmod +x start.sh

# Set permissions again after copying
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8001

# Use simple startup script
CMD ["./start.sh"] 