#!/bin/sh
set -eu

sudo apt-get update
sudo curl -fsSL \
	-o /usr/local/bin/spc \
	"https://dl.static-php.dev/static-php-cli/spc-bin/nightly/spc-linux-$(uname -m)"
sudo chmod +x /usr/local/bin/spc

cd /tmp
sudo spc doctor --auto-fix --no-interaction