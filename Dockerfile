FROM php:7.1-fpm-alpine

ENV APCU_VERSION 5.1.7

# Git is for composer
# su-exec for managing permissions
RUN apk add --no-cache --virtual build-dependencies \
	autoconf \
	build-base \
	icu-dev \
	zlib-dev

RUN apk --update --no-cache add \
        git \
        icu-libs \
        openssl \
        su-exec \
        zlib

RUN docker-php-ext-install \
        mbstring \
        intl \
        pdo_mysql \
        zip \
	&& pecl install apcu-${APCU_VERSION} \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache

RUN apk del build-dependencies

COPY ./docker/php/php.ini /etc/php7/php.ini

# install composer
COPY ./docker/php/composer.sh composer.sh
RUN chmod +x composer.sh \
	&& sh composer.sh \
	&& mv composer.phar /usr/bin/composer \
	&& chmod +x /usr/bin/composer

# prepare volume directory
RUN mkdir /api-platform

# speed up composer
RUN su-exec www-data composer global require hirak/prestissimo:^0.3

WORKDIR /api-platform

COPY composer.json .
COPY composer.lock .

RUN chown -R www-data .

RUN su-exec www-data composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
        && composer clear-cache

COPY ./docker/php/docker-cmd.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-cmd.sh
CMD ["docker-cmd.sh"]
