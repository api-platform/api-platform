#!/bin/sh

# Remove all running containers and caches
docker compose down -v
docker system prune -a --volumes

# Update Docker images
docker compose pull --include-deps
docker compose build --no-cache

# Update deps
docker compose run php composer update
docker compose run pwa /bin/sh -c 'pnpm install; pnpm update'

# Update Symfony recipes
cd api
composer recipes:update

echo 'Run `git diff` and carefully inspect the changes made by the recipes.'
echo 'Run `docker compose up --force-recreate` now and check that everything is fine!'
