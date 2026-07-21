# ================================
# Stage 1: Node - Build frontend assets
# ================================
FROM node:20-alpine AS node-builder

WORKDIR /app

# ARG non-sensitif tetap seperti biasa (host/port/scheme bukan data rahasia)
ARG VITE_REVERB_HOST
ARG VITE_REVERB_PORT
ARG VITE_REVERB_SCHEME
ENV VITE_REVERB_HOST=$VITE_REVERB_HOST
ENV VITE_REVERB_PORT=$VITE_REVERB_PORT
ENV VITE_REVERB_SCHEME=$VITE_REVERB_SCHEME

COPY package.json package-lock.json* ./
RUN npm ci --frozen-lockfile

COPY . .

# VITE_REVERB_APP_KEY di-mount sebagai BuildKit secret, BUKAN ARG/ENV.
# Nilainya cuma tersedia selama command RUN ini jalan, lalu hilang total
# (tidak ke-cache permanen di layer image manapun) — makanya tidak ada
# lagi warning "SecretsUsedInArgOrEnv".
RUN --mount=type=secret,id=vite_reverb_app_key \
    export VITE_REVERB_APP_KEY=$(cat /run/secrets/vite_reverb_app_key) && \
    npm run build

# ================================
# Stage 2: PHP - App
# ================================
FROM php:8.3-fpm-alpine AS base

# Install system dependencies (minimal)
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    mysql-dev \
    icu-dev \
    shadow

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        mbstring \
        bcmath \
        opcache \
        intl \
        pcntl

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN addgroup -g 1000 -S laravel && adduser -u 1000 -S laravel -G laravel

WORKDIR /var/www/html

# ================================
# Stage 3: Development
# ================================
FROM base AS development

# Copy PHP config for dev
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# Install composer deps (with dev)
COPY --chown=laravel:laravel composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy app
COPY --chown=laravel:laravel . .

# Copy built assets from node stage
COPY --from=node-builder --chown=laravel:laravel /app/public/build ./public/build

RUN composer dump-autoload --optimize

# Storage & cache permissions
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views,testing} bootstrap/cache \
    && chown -R laravel:laravel storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER laravel

EXPOSE 9000
CMD ["php-fpm"]

# ================================
# Stage 4: Production
# ================================
FROM base AS production

# Copy PHP config for production (OPcache enabled)
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf

# Install composer deps (no dev)
COPY --chown=laravel:laravel composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

# Copy app
COPY --chown=laravel:laravel . .

# Copy built assets from node stage
COPY --from=node-builder --chown=laravel:laravel /app/public/build ./public/build

RUN composer dump-autoload --optimize --classmap-authoritative

# Storage & cache permissions
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views,testing} bootstrap/cache \
    && chown -R laravel:laravel storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script (untuk handle migration dan cache di container)
COPY --chown=laravel:laravel docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER laravel

EXPOSE 9000

# Entrypoint: jalankan artisan cache SETELAH .env di-inject
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
