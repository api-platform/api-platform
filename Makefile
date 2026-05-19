.PHONY: install test build clean

DIST ?= dist
TARGET ?= $(DIST)/api-platform
PHAR := $(DIST)/api-platform.phar

install:
	composer install --no-interaction --no-progress

test:
	vendor/bin/phpunit

# Build a static binary using crazywhalecc/static-php-cli.
# Requires `spc` to be available on PATH (download from https://dl.static-php.dev).
build:
	mkdir -p $(DIST)
	composer install --no-dev --no-interaction --no-progress --optimize-autoloader
	php -d phar.readonly=0 scripts/build-phar.php $(PHAR)
	composer install --no-interaction --no-progress
	spc download --for-extensions=phar,filter,tokenizer,mbstring,ctype,zlib,curl,openssl
	spc build "phar,filter,tokenizer,mbstring,ctype,zlib,curl,openssl" --build-micro
	spc micro:combine $(PHAR) -O $(TARGET)
	rm -f $(PHAR)

clean:
	rm -rf $(DIST) buildroot
