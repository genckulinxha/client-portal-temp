# syntax=docker/dockerfile:1.6

FROM php:8.3-cli-alpine AS base

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    bash git curl wget libzip-dev oniguruma-dev icu-dev sqlite-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite intl exif zip gd \
    && apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create essential directories first
RUN mkdir -p storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/framework/testing \
             storage/logs \
             bootstrap/cache

# Copy only composer files first for caching, then install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts --no-dev --optimize-autoloader

# Copy the rest of the app (excluding vendor, node_modules via .dockerignore)
COPY . .

# Install dev dependencies and finalize autoloader
RUN composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader

# Set proper permissions for storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache

ENV PORT=8001 HOST=0.0.0.0

# Entry script will handle key generation, migrations and seeding on first run
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8001

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8001"] 