# Stage 1: Build frontend assets
FROM node:18-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY webpack.mix.js tailwind.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm run production

# Stage 2: PHP / Apache application
FROM php:8.4-apache AS app

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache DocumentRoot to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source
COPY . .

# Copy compiled assets from stage 1
COPY --from=assets /app/public/css public/css
COPY --from=assets /app/public/js public/js
COPY --from=assets /app/public/mix-manifest.json public/mix-manifest.json

# Generate optimised autoloader & run post-install scripts
RUN composer dump-autoload --optimize \
    && composer run-script post-autoload-dump

# Set permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
