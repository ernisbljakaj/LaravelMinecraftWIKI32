FROM node:22-alpine AS frontend

WORKDIR /build

ARG NPM_TOKEN=

COPY package.json package-lock.json .npmrc ./
RUN npm ci --ignore-scripts=false && npm cache clean --force

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
RUN npm run build

FROM composer:2 AS composer

FROM php:8.5-fpm AS vendor

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libzip-dev \
        zlib1g-dev \
        unzip \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install exif \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY --chown=www-data:www-data . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

FROM php:8.5-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        libzip-dev \
        zlib1g-dev \
        unzip \
        git \
        nginx \
        gettext-base \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install exif \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/pear

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=frontend --chown=www-data:www-data /build/public/build /var/www/html/public/build

RUN mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R ug+rwX storage bootstrap/cache database

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template

ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_URL=https://localhost \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=database \
    LOG_CHANNEL=stderr \
    PORT=10000

EXPOSE 10000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]