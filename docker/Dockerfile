# =============================================================================
# Dockerfile — Task Manager Laravel
# NIVEL 4: Imagen multi-etapa con PHP-FPM 8.3, Composer, Node y extensiones
# =============================================================================

# ── ETAPA 1: Dependencias PHP ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS build

RUN apk add --no-cache \
    git unzip curl libzip-dev oniguruma-dev autoconf g++ make linux-headers \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath pcntl

# Instalar Redis extension desde PECL
RUN pecl install redis && docker-php-ext-enable redis

# ── ETAPA 2: Dependencias Node ────────────────────────────────────────────
FROM node:22-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json* vite.config.js postcss.config.js tailwind.config.js ./
RUN npm ci
COPY resources/ resources/
RUN npm run build

# ── ETAPA 3: Aplicación final ─────────────────────────────────────────────
FROM php:8.3-fpm-alpine AS app

# Paquetes del sistema
RUN apk add --no-cache \
    git unzip curl libzip-dev oniguruma-dev nginx supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath pcntl \
    && pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar fuente de la aplicación
COPY . .

# Copiar assets compilados desde la etapa Node
COPY --from=node-build /app/public/build /var/www/public/build

# Instalar dependencias PHP (producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Cleanup
RUN rm -rf /root/.composer /tmp/*

# Supervisor configuración
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 9000

CMD ["php-fpm"]
