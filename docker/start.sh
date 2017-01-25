#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

# Warning, as this container runs as root we need to use composer as www-data or permissions will be broken
su -s /bin/bash -c "composer global require 'hirak/prestissimo:^0.3'" www-data

if [ "$SYMFONY_ENV" = 'dev' ]; then
    su -s /bin/bash -c "composer install --prefer-dist --no-progress --no-suggest" www-data
else
    su -s /bin/bash -c "composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative" www-data
fi

exec apache2-foreground
