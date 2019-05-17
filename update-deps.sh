#!/bin/sh

# Update Docker images
docker-compose pull
docker-compose build

# Update deps
docker-compose run php composer update
docker-compose run admin /bin/sh -c 'yarn install && yarn upgrade'
docker-compose run client /bin/sh -c 'yarn install && yarn upgrade'

# Update the Symfony skeleton
cd api
composer sync-recipes --force

# Hint the user to change APP_SECRET
sed -i.bak 's/^APP_SECRET=.*$/APP_SECRET=!ChangeMe!/' .env
# Compatibility with our Docker and Kubernetes setup
sed -i.bak 's;^#TRUSTED_PROXIES=127.0.0.1,127.0.0.2$;TRUSTED_PROXIES=10.0.0.0/8,172.16.0.0/12,192.168.0.0/16;' .env
sed -i.bak "s/^#TRUSTED_HOSTS='\^localhost\|example\\\.com\$'$/TRUSTED_HOSTS='^localhost|api$'/" .env
rm .env.bak
sed -i.bak 's/ \^ Request::HEADER_X_FORWARDED_HOST//' public/index.php
rm public/index.php.bak

echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
