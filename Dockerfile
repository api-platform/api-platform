FROM php:7.1-fpm-alpine

RUN apk add --no-cache --virtual .persistent-deps \
		git \
		icu-libs \
		zlib

ENV APCU_VERSION 5.1.8

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		zlib-dev \
	&& docker-php-ext-install \
		intl \
		pdo_mysql \
		zip \
	&& pecl install \
		apcu-${APCU_VERSION} \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& apk del .build-deps

COPY docker/app/php.ini /usr/local/etc/php/php.ini

COPY docker/app/install-composer.sh /usr/local/bin/docker-app-install-composer
RUN chmod +x /usr/local/bin/docker-app-install-composer

RUN set -xe \
	&& apk add --no-cache --virtual .fetch-deps \
		openssl \
	&& docker-app-install-composer \
	&& mv composer.phar /usr/local/bin/composer \
	&& apk del .fetch-deps

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative \
	&& composer clear-cache

WORKDIR /srv/api-platform

COPY composer.json ./
COPY composer.lock ./

RUN mkdir -p \
		var/cache \
		var/logs \
		var/sessions \
	&& composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
	&& composer clear-cache \
# Permissions hack because setfacl does not work on Mac and Windows
	&& chown -R www-data var

COPY app app/
COPY bin bin/
COPY src src/
COPY web web/

RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

COPY docker/app/docker-entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]
