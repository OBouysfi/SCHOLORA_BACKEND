FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# System dependencies
RUN echo "UTC" > /etc/timezone \
 && apk add --no-cache \
    bash \
    curl \
    git \
    vim \
    zip \
    unzip \
    nginx \
    supervisor \
    libpng \
    libpng-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

# PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    pdo_mysql \
    gd \
    zip

# Redis extension (phpredis)
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer (official, fast)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Supervisor
RUN mkdir -p /etc/supervisor.d
COPY ./docker/configs/supervisord.ini /etc/supervisor.d/supervisord.ini

# PHP & Nginx config
COPY ./docker/configs/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./docker/configs/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/configs/nginx.conf /etc/nginx/
COPY ./docker/configs/nginx-laravel.conf /etc/nginx/modules/

# Upload limits
RUN echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini

# App source
COPY ./src .

# Install PHP deps
RUN composer install --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
