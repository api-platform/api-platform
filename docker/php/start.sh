#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

if [ "$SYMFONY_ENV" = 'prod' ]; then
	composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative
else
	composer install --prefer-dist --no-progress --no-suggest
fi

# Permissions hack because setfacl does not work on Mac and Windows
chown -R www-data var

exec php-fpm
