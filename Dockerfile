# ── Stage 1: Build frontend assets ──────────────────────────
FROM node:20-alpine AS assets

WORKDIR /build

COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# ── Stage 2: PHP application ───────────────────────────────
FROM php:8.4-fpm-alpine

# Install system deps + PHP extensions
RUN apk add --no-cache \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        mariadb-connector-c-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        bcmath \
        opcache \
        zip \
        intl \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# Copy application source
COPY . .

# Re-run composer scripts (post-autoload-dump etc.)
RUN composer dump-autoload --optimize

# Copy built frontend assets from stage 1
COPY --from=assets /build/public/build ./public/build

# Copy custom PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/99-recip.ini

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Stash built public assets so entrypoint can sync them into the volume
RUN cp -a /var/www/html/public /var/www/html/public-build

# Copy and set entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
