#!/bin/sh

docker-compose pull
docker-compose build
docker-compose run php composer update
docker-compose run admin /bin/sh -c 'yarn install && yarn upgrade'
docker-compose run client /bin/sh -c 'yarn install && yarn upgrade'
cd api && composer sync-recipes --force
echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
