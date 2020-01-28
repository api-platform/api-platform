#!/bin/sh
set -e

/php-cs-fixer fix src/ --verbose --rules=@Symfony,-no_superfluous_phpdoc_tags,-no_unused_imports
