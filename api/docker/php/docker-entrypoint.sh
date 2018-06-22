#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
		>&2 echo "Waiting for Postgres to be ready..."
		until pg_isready --timeout=0 --dbname="${DATABASE_URL}"; do
			sleep 1
		done
		bin/console doctrine:schema:update --force --no-interaction
	fi
fi

exec docker-php-entrypoint "$@"
