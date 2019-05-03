#!/bin/sh

docker-compose pull
docker-compose build
docker-compose run php /bin/sh -c 'composer update && composer sync-recipes --force'
docker-compose run admin /bin/sh -c 'yarn install && yarn upgrade'
docker-compose run client /bin/sh -c 'yarn install && yarn upgrade'
echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
