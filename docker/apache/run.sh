#!/bin/sh
set -xe

# Detect the host IP
export DOCKER_BRIDGE_IP=$(ip ro | grep default | cut -d' ' -f 3)

# Start Apache with the right permissions
/app/docker/apache/start_safe_perms -DFOREGROUND
