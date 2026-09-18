# =========================
# 1. Build frontend assets
# =========================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build


# =========================
# 2. Laravel + Apache
# =========================
FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Bring the Vite production build from the Node stage
COPY --from=frontend /app/public/build ./public/build

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render provides PORT when the container runs
CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]