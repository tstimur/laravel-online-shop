FROM php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        pdo \
        pdo_pgsql \
        pgsql \
        xml \
        bcmath \
        mbstring \
        tokenizer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# User configuration
ARG USER_ID=1000
RUN usermod -u ${USER_ID} www-data && groupmod -g ${USER_ID} www-data

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

USER www-data