.PHONY: install test phar build clean

PHAR ?= bin/api-platform.phar
DIST ?= dist
TARGET ?= $(DIST)/api-platform

install:
	composer install --no-interaction --no-progress

test:
	vendor/bin/phpunit

# Build a self-contained PHAR using box (https://github.com/box-project/box).
phar:
	composer install --no-dev --no-interaction --no-progress --optimize-autoloader
	box compile
	composer install --no-interaction --no-progress

# Build a static binary using crazywhalecc/static-php-cli.
# Requires `spc` to be available on PATH (download from https://dl.static-php.dev).
build: phar
	mkdir -p $(DIST)
	spc download --for-extensions=phar,filter,tokenizer,mbstring,ctype,zlib,curl,openssl
	spc build "phar,filter,tokenizer,mbstring,ctype,zlib,curl,openssl" --build-micro
	spc micro:combine $(PHAR) -O $(TARGET)

clean:
	rm -rf $(DIST) $(PHAR) buildroot
