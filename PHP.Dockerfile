FROM php:fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libmcrypt-dev \
    libssl-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Install PHP dependencies at build time
COPY app/composer.json app/composer.lock* ./
RUN composer config policy.advisories.block false && composer update --no-dev --optimize-autoloader --no-interaction
