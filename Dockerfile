# AUN E-Laundry: production image for Render (PHP 8.2 + Apache)

# 1. PHP dependencies (no dev packages)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

# 2. Front-end assets (Tailwind also scans Laravel's pagination views in vendor/)
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

# 3. Runtime
FROM php:8.2-apache
# Apache's alias module maps /icons/ to its own icon folder, which hid the app's /icons/*.png
# (so Android would not offer to install the app). Laravel does not need it, so it is disabled.
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev libzip-dev unzip \
 && docker-php-ext-install pdo_pgsql pdo_mysql zip opcache \
 && a2enmod rewrite headers \
 && a2dismod -f alias \
 && rm -rf /var/lib/apt/lists/*

# Serve Laravel's public/ folder on the port Render gives us ($PORT, 10000 by default)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    PORT=10000
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri 's!Listen 80!Listen ${PORT}!' /etc/apache2/ports.conf \
 && sed -ri 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize --no-dev \
 && php artisan package:discover --ansi \
 && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
 && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start
EXPOSE 10000
CMD ["start"]
