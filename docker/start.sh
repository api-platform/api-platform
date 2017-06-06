#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

if [ "$SYMFONY_ENV" = 'prod' ]; then
    composer install --prefer-dist --no-dev --no-progress --no-suggest --optimize-autoloader --classmap-authoritative
else
    composer install --prefer-dist --no-progress --no-suggest
fi

#get apache user : second word of the first line (from docker/apache/start_safe_perms)
apache_user=$(head -n 1 /etc/apache2/apache2.conf| cut -d " " -f 2)
#get apache group : second word of the second line
apache_group=$(head -n 2 /etc/apache2/apache2.conf| tail -1 |  cut -d " " -f 2)

chown -R $apache_user:$apache_group var

# Start Apache with the right permissions after removing pre-existing PID file
rm -f /var/run/apache2/apache2.pid
exec docker/apache/start_safe_perms -DFOREGROUND
