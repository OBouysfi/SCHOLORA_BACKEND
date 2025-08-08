FROM php:8.4.8RC1-fpm-alpine

WORKDIR /var/www/html/

# Essentials
RUN echo "UTC" > /etc/timezone
RUN apk add --no-cache zip unzip curl sqlite nginx supervisor git vim libpng libpng-dev npm libzip-dev

# Install PostgreSQL dependencies for Alpine
RUN apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo_pgsql pgsql

# Installing bash
RUN apk add bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

RUN apk add --no-cache php83-fpm \
    php82-pecl-xdebug  \
    php82-common \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-json \
    php82-xml \
    php82-xmlwriter \
    php82-simplexml \
    php82-dom \
    php82-pdo_sqlite \
    php82-tokenizer \
    php82-pecl-redis \
    php82-gd 

RUN docker-php-ext-install gd pdo_mysql
RUN docker-php-ext-install zip

# Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Configure supervisor
RUN mkdir -p /etc/supervisor.d/
COPY ./docker/configs/supervisord.ini /etc/supervisor.d/supervisord.ini

COPY ./docker/configs/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./docker/configs/php-development.ini /etc/php83/php.ini
COPY ./docker/configs/www.conf /usr/local/etc/php-fpm.d/www.conf

# Configure PHP for large uploads
RUN echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Configure nginx
COPY ./docker/configs/nginx.conf /etc/nginx/
COPY ./docker/configs/nginx-laravel.conf /etc/nginx/modules/

RUN mkdir -p /run/nginx/
RUN touch /run/nginx/nginx.pid

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

COPY ./src .
COPY ./docker/configs/laravel.env .env
RUN composer install
RUN composer dump-autoload

RUN chown -R nobody:nobody /var/www/html/
RUN chmod 775 -R /var/lib/nginx

EXPOSE 80
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]