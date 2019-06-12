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
sed -i.bak 's;^DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name$;DATABASE_URL=postgres://api-platform:!ChangeMe!@db/api;' .env
rm .env.bak
sed -i.bak 's/ \^ Request::HEADER_X_FORWARDED_HOST//' public/index.php
rm public/index.php.bak
# Doctrine recipe
sed -i.bak "s/driver: 'pdo_mysql'$/driver: 'pdo_pgsql'/" config/packages/doctrine.yaml
sed -i.bak "s/server_version: '5.7'$/server_version: '10'/" config/packages/doctrine.yaml
sed -i.bak "/utf8mb4/d" config/packages/doctrine.yaml
sed -i.bak "/default_table_options:$/d" config/packages/doctrine.yaml
rm config/packages/doctrine.yaml.bak

echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
