FROM php:8.0.29-fpm-alpine3.16

RUN apk add --no-cache bash shadow linux-headers

ENV PHALCON_VERSION=5.1.4
ENV XDEBUG_VERSION=3.4.1

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        yaml-dev \
        # intl:
        icu-dev \
        # zip:
        libzip-dev \
        # postgres
        libpq-dev \
    && docker-php-ext-install \
        opcache \
        intl \
        zip \
        pdo_pgsql \
    && pecl install yaml-2.2.3 \
    && pecl install phalcon-$PHALCON_VERSION \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable \
        yaml \
        phalcon \
        xdebug \
    && apk del .phpize-deps

# composer
COPY --from=composer:2.5.8 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
ENV DOCKER_APP_UID=1000
RUN usermod -u $DOCKER_APP_UID www-data
USER www-data
