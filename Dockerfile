FROM phpdockerio/php71-fpm

# Install MySQL PDO extension
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.1-mysql php7.1-mbstring iproute2 git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY docker/php/php.ini /usr/local/etc/php/php.ini

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

COPY docker/php/start.sh /usr/local/bin/docker-app-start

RUN chmod +x /usr/local/bin/docker-app-start

CMD ["docker-app-start"]
