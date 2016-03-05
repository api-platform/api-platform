#!/bin/sh
set -xe

# Start Apache with the right permissions
/app/docker/apache/start_safe_perms -DFOREGROUND
