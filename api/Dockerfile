#syntax=docker/dockerfile:1.4

# Adapted from https://github.com/dunglas/symfony-docker


# Versions
FROM php:8.2-fpm-alpine AS php_base
FROM mlocati/php-extension-installer:2 AS php_extension_installer_base
FROM composer/composer:2-bin AS composer_base
FROM caddy:2-alpine AS caddy_base
FROM curlimages/curl:8.1.2 AS curl_base


# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# Base image
FROM php_base AS app_php_base

WORKDIR /srv/app

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=php_extension_installer_base --link /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
# hadolint ignore=DL3018
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
	;

RUN set -eux; \
    install-php-extensions \
		apcu \
		intl \
		opcache \
		zip \
    ;

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN set -eux; \
    install-php-extensions pdo_pgsql
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/

COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer_base --link /composer /usr/bin/composer


# Dev image
FROM app_php_base AS app_php_dev

ENV APP_ENV=dev XDEBUG_MODE=off
VOLUME /srv/app/var/

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
    	xdebug \
    ;

COPY --link docker/php/conf.d/app.dev.ini $PHP_INI_DIR/conf.d/

# Prod image
FROM app_php_base AS app_php_prod

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

# prevent the reinstallation of vendors at every changes in the source code
COPY --link composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link . ./
RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;


# Download Caddy with the Mercure and Vulcain modules
FROM curl_base AS caddy_downloader

WORKDIR /downloads

RUN curl \
	--url-query "os=$TARGETOS" \
	--url-query "arg=$TARGETARCH" \
	--url-query 'p=github.com/dunglas/mercure/caddy' \
	--url-query 'p=github.com/dunglas/vulcain/caddy' \
	-o /downloads/caddy \
	https://caddyserver.com/api/download


# Caddy image
FROM caddy_base AS app_caddy

WORKDIR /srv/app

COPY --from=caddy_downloader --chmod=500 /downloads/caddy /usr/bin/caddy
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile
COPY --from=app_php_prod --link /srv/app/public public/
