#!/bin/sh

# Copyright (c) Nils Adermann, Jordi Boggiano
# Origin: https://github.com/composer/composer/blob/master/doc/faqs/how-to-install-composer-programmatically.md
# Licence: MIT

EXPECTED_SIGNATURE=$(php -r "echo trim(file_get_contents('https://composer.github.io/installer.sig'));")
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');")

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
then
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
rm composer-setup.php
exit $RESULT
