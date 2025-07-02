ARG PHP_IMAGE=8.0.29-fpm-alpine3.16
FROM php:$PHP_IMAGE

RUN apk add --no-cache bash shadow linux-headers

ENV XDEBUG_VERSION=3.4.1

# composer
COPY --from=composer:2.8.9 /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        yaml-dev \
        # intl:
        icu-dev \
        # pgsql:
        libpq-dev \
    && docker-php-ext-install \
        opcache \
        intl \
        pdo_pgsql \
    && pecl channel-update pecl.php.net \
    && pecl install yaml-2.2.4 \
    && pecl install xdebug-$XDEBUG_VERSION \
    && docker-php-ext-enable \
        yaml \
        xdebug \
    && apk del .phpize-deps

# https://github.com/phalcon/cphalcon/issues/16680#issuecomment-2779809115
# https://github.com/phalcon/docker/issues/4#issuecomment-2780032550
ENV CFLAGS="-Wno-error=incompatible-pointer-types"

ARG PHALCON_VERSION=5.1.4
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install phalcon-$PHALCON_VERSION && docker-php-ext-enable phalcon \
    && apk del .phpize-deps

WORKDIR /var/www
ENV DOCKER_APP_UID=1000
RUN usermod -u $DOCKER_APP_UID www-data
USER www-data
