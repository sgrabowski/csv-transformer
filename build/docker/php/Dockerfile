# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/build/#target

# Builder images
FROM composer/composer:2-bin AS composer
FROM mlocati/php-extension-installer:latest AS php_extension_installer
FROM php:8.3-fpm-alpine AS php_base

ARG BUILD_TAG=NOT_DEFINED

ENV BUILD_TAG=${BUILD_TAG}
ENV APP_ENV=prod
WORKDIR /srv/app

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=php_extension_installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk update

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        bash \
        curl \
        wget \
        sed \
        vim \
        iputils \
        bash \
        nmap \
        jq \
    ;

RUN apk add --no-cache --update docker openrc

RUN set -eux; \
    install-php-extensions \
        intl \
        opcache \
        zip \
        pdo_mysql \
        redis \
    ;

# $PHP_INI_DIR is defined in the base php image
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY build/docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY build/docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

COPY build/docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

RUN mkdir -p /var/run/php

COPY build/docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer /composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.* ./
RUN set -eux; \
    if [ -f composer.json ]; then \
        composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
        composer clear-cache; \
    fi

COPY . ./

RUN set -eux; \
    mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
        composer dump-autoload --classmap-authoritative; \
        composer dump-env prod; \
        composer run-script post-install-cmd; \
        chmod +x bin/console; sync; \
    fi

COPY . ./

RUN mkdir -p /srv/app/var/cache /srv/app/var/log \
    && chown -R www-data:www-data /srv/app/var

# Dev image
FROM php_base AS php_dev

ARG BUILD_TAG=NOT_DEFINED

ENV BUILD_TAG=${BUILD_TAG}
ENV APP_ENV=dev

# Remove production config, move development config to use
RUN rm "$PHP_INI_DIR/conf.d/app.prod.ini"; \
    mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install Xdebug
RUN install-php-extensions xdebug

COPY build/docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

RUN rm -f .env.local.php

WORKDIR /srv/app

COPY --from=php_base /srv/app/public public/
