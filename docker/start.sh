#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

if [ "$SYMFONY_ENV" = 'dev' ]; then
    composer install --prefer-dist --no-progress --no-suggest
else
    composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader
fi

# Start Apache with the right permissions
exec docker/apache/start_safe_perms -DFOREGROUND
