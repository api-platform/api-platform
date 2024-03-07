#!/bin/sh

# Remove all running containers
docker compose down -v

# Update Docker images
docker compose build --no-cache --pull

# Update deps
docker compose run php /bin/sh -c 'composer update; composer outdated'
docker compose run pwa /bin/sh -c 'pnpm install; pnpm update; pnpm outdated'

# Update Symfony recipes
cd api
composer recipes:update

# Run tests
docker compose run php /bin/sh -c 'bin/console -e test doctrine:database:create ; bin/console -e test doctrine:migrations:migrate --no-interaction ; bin/phpunit ; bin/console -e test doctrine:schema:validate'

echo 'Run `git diff` and carefully inspect the changes made by the recipes.'
echo 'Run `docker compose up --force-recreate` now and check that everything is fine!'
