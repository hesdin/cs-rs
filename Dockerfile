# ============================================================
# Stage 1: Install PHP dependencies + generate Wayfinder types
# ============================================================
FROM php:8.4-cli-alpine AS composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
    libpq-dev \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath mbstring zip intl gd

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-autoloader --no-scripts --prefer-dist

COPY . .
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# ============================================================
# Stage 2: Build frontend assets (with PHP for Wayfinder)
# ============================================================
FROM node:22-alpine AS frontend

# Install PHP for Wayfinder type generation
RUN apk add --no-cache \
    php84 \
    php84-cli \
    php84-pdo \
    php84-pgsql \
    php84-mbstring \
    php84-xml \
    php84-curl \
    php84-zip \
    php84-gd \
    php84-intl \
    php84-opcache \
    php84-dom \
    php84-tokenizer \
    php84-fileinfo \
    php84-phar \
    && ln -sf /usr/bin/php84 /usr/bin/php

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy PHP dependencies from composer stage
COPY --from=composer /app/vendor ./vendor
COPY --from=composer /app/bootstrap ./bootstrap

COPY package.json package-lock.json ./
COPY scripts/ ./scripts/

RUN npm ci --ignore-scripts && npm run postinstall

COPY . .

RUN npm run build

# ============================================================
# Stage 3: PHP-FPM Application
# ============================================================
FROM php:8.4-fpm-alpine AS production

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    linux-headers \
    supervisor \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    bcmath \
    mbstring \
    zip \
    pcntl \
    opcache \
    intl \
    gd

# Install Redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files from stages
COPY --from=composer /app/vendor ./vendor
COPY --from=frontend /app/node_modules ./node_modules
COPY --from=frontend /app/public/build ./public/build
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copy PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create log directories
RUN mkdir -p /var/log/supervisor /var/log/php

# Expose port
EXPOSE 9000

# Start Supervisor (manages PHP-FPM + queue worker)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
