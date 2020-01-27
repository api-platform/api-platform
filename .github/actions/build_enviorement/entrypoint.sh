#!/bin/sh
set -e;
docker-compose run --no-deps --rm -T php composer validate --no-check-publish;
docker-compose up -d api db;
