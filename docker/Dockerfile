FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip curl libzip-dev oniguruma-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
