#!/bin/sh
set -xe

chown -R www-data .

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

if [ "$SYMFONY_ENV" = 'prod' ]; then
    su-exec www-data composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative
else
    su-exec www-data composer install --prefer-dist --no-progress --no-suggest
fi

exec php-fpm -F
