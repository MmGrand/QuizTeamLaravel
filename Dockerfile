FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libonig-dev \
        libzip-dev \
        libsqlite3-dev \
        libpq-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install pdo_sqlite pdo_mysql pdo_pgsql pcntl mbstring zip gd intl \
    && rm -rf /var/lib/apt/lists/*

# Node нужен в том же образе, что и PHP: плагин Wayfinder во время `vite build`
# вызывает `php artisan wayfinder:generate`, поэтому собрать фронт в отдельном
# node-контейнере нельзя. Образ php:8.4-cli основан на Debian bookworm, так что
# бинарники из node:24-bookworm-slim совместимы по glibc.
COPY --from=node:24-bookworm-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:24-bookworm-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-interaction --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
