#!/bin/sh
set -e

export SCRIPT_NAME=/ping
export SCRIPT_FILENAME=/ping
export REQUEST_METHOD=GET

if cgi-fcgi -bind -connect /var/run/php/php-fpm.sock; then
	exit 0
fi

exit 1
