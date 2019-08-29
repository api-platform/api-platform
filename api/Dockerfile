# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=7.3
ARG NGINX_VERSION=1.17
ARG VARNISH_VERSION=6.2

# Leave it blank to use debian
# fill with "-alpine" to use alpine
ARG ALPINE='-alpine'

# "php" stage
FROM php:${PHP_VERSION}-fpm${ALPINE} AS api_platform_php

ARG ALPINE

# persistent / runtime deps
ENV PERSISTENT_DEPS \
		acl \
		file \
		gettext \
		git

RUN if [ ${ALPINE} == "-alpine" ]; then \
		apk add --no-cache \
			$PERSISTENT_DEPS \
		; \
	else \
		apt-get update; \
		apt-get install -y --no-install-recommends \
			$PERSISTENT_DEPS \
		; \
		rm -rf /var/lib/apt/lists/*; \
	fi

ENV PHP_EXT_DEPS \
		$PHPIZE_DEPS \
		libzip-dev

ARG APCU_VERSION=5.1.17
RUN set -eux; \
	if [ ${ALPINE} == "-alpine" ]; then \
		apk add --no-cache --virtual .build-deps \
			$PHP_EXT_DEPS \
			icu-dev \
			postgresql-dev \
			zlib-dev \
		; \
	else \
		savedAptMark="$(apt-mark showmanual)"; \
		apt-get update; \
		apt-get install -y --no-install-recommends \
			$PHP_EXT_DEPS \
			libicu-dev \
			libpq-dev \
			zlib1g-dev \
		; \
		rm -rf /var/lib/apt/lists/*; \
	fi; \
	\
	docker-php-ext-configure zip --with-libzip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_pgsql \
		zip \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
	; \
	\
	if [ ${ALPINE} == "-alpine" ]; then \
		runDeps="$( \
			scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
				| tr ',' '\n' \
				| sort -u \
				| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
		)"; \
		apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
		\
		apk del .build-deps; \
	else \
		apt-mark auto '.*' > /dev/null; \
		[ -z "$savedAptMark" ] || apt-mark manual $savedAptMark; \
		find /usr/local -type f -executable -exec ldd '{}' ';' \
			| awk '/=>/ { print $(NF-1) }' \
			| sort -u \
			| xargs -r dpkg-query --search \
			| cut -d: -f1 \
			| sort -u \
			| xargs -r apt-mark manual \
		; \
		apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	fi

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/api-platform.ini $PHP_INI_DIR/conf.d/api-platform.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/api

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
	composer clear-cache

# do not use .env files in production
COPY .env ./
RUN composer dump-env prod; \
	rm .env

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/api/var

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


# "nginx" stage
# depends on the "php" stage above
FROM nginx:${NGINX_VERSION}-alpine AS api_platform_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/api

COPY --from=api_platform_php /srv/api/public public/


# "varnish" stage
# does not depend on any of the above stages, but placed here to keep everything in one Dockerfile
FROM cooptilleuls/varnish:${VARNISH_VERSION}-alpine AS api_platform_varnish

COPY docker/varnish/conf/default.vcl /usr/local/etc/varnish/default.vcl
