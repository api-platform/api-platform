#!/bin/sh

# Update Docker images
docker-compose pull
docker-compose build

# Update deps
docker-compose run php composer update
docker-compose run pwa /bin/sh -c 'yarn install && yarn upgrade'

# Update the Symfony skeleton
cd api
composer sync-recipes --force

# Hint the user to change APP_SECRET
sed -i.bak 's/^APP_SECRET=.*$/APP_SECRET=!ChangeMe!/' .env
# Compatibility with our Docker and Kubernetes setup
sed -i.bak "s;^#TRUSTED_PROXIES;TRUSTED_PROXIES;" .env
sed -i.bak "s;^#TRUSTED_HOSTS='^(localhost|example\\\.com)\$'$;TRUSTED_HOSTS='^(localhost|caddy)\$';" .env
sed -i.bak 's;^postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=13&charset=utf8$;DATABASE_URL=postgresql://api-platform:!ChangeMe!@database:5432/api?serverVersion=13&charset=utf8;' .env
sed -i.bak 's;^MERCURE_PUBLISH_URL=http://mercure/.well-known/mercure$;MERCURE_PUBLISH_URL=http://caddy/.well-known/mercure;' .env

rm .env.bak

sed -i.bak 's/ \^ Request::HEADER_X_FORWARDED_HOST//' public/index.php
rm public/index.php.bak

echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
