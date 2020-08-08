#!/bin/sh

set -e

cp -r /usr/src/cache/node_modules/. /usr/src/admin/node_modules/

exec yarn start
