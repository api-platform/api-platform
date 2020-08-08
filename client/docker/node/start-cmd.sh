#!/bin/sh

set -e

cp -r /usr/src/cache/node_modules/. /usr/src/client/node_modules/

exec yarn start
