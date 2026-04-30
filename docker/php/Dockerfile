FROM php:8.2-fpm

ENV ASUSER=www-data
ENV UID=33
ENV GID=33

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev libicu-dev libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app files
COPY . /var/www

# ✅ Run composer install at BUILD TIME as root (before chown)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Fix permissions
RUN chown -R www-data:www-data /var/www

# Copy FPM config
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Override zz-docker.conf to force IPv4 binding
RUN echo '[global]\ndaemonize = no\n\n[www]\nlisten = 0.0.0.0:9000' > /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
