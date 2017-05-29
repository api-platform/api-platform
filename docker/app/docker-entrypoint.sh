#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	# Detect the host IP
	export DOCKER_BRIDGE_IP
	DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

	if [ "$SYMFONY_ENV" = 'prod' ]; then
		composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative --no-interaction
	else
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
	fi

	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data var
fi

exec docker-php-entrypoint "$@"
