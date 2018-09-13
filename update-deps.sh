#!/bin/sh

docker-compose pull
docker-compose build
docker-compose run php composer update
docker-compose run admin yarn upgrade
docker-compose run client yarn upgrade
echo 'Run `docker-compose up --build --force-recreate` now and check that everything is fine!'
