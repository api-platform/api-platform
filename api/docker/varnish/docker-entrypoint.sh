#!/bin/sh
set -e

envsubst '\$BACKEND \$UPSTREAM' < /usr/local/etc/varnish/default.tmpl > /etc/varnish/default.vcl

exec "$@"
