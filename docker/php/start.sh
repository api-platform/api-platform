#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

# Permissions hack because setfacl does not work on Mac and Windows
chown -R www-data var

# Remove extraneous fpm envelopes to allow for true application logging to stdout, as per docker best practices
# (restores what's done on the base fpm image)
/usr/sbin/php-fpm7.1 -F -O 2>&1 | sed -u 's,.*: \"\(.*\)$,\1,'| sed -u 's,"$,,' 1>&1
