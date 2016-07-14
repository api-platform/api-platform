FROM gorghoa/php7

ADD . /app
WORKDIR /app

RUN openssl genrsa -passout pass:api-platform -out /root/private.jwt.pem -aes256 4096 \
    && openssl rsa -passin pass:api-platform -pubout -in /root/private.jwt.pem -out /root/public.jwt.pem

ARG INSTALL_DEP=
ARG SYMFONY_ENV=dev
ARG NODE_ENV=

RUN if [ -n "$INSTALL_DEP" ]; then \
	if [ "$SYMFONY_ENV" -ne "prod" ]; then \
        composer install -o --no-interaction --no-dev; \
	else \
	composer install -o --no-interaction --prefer-dist; \
	fi; \
    fi;

EXPOSE 8000

ADD docker/php/php.ini /etc/php/7.0/cli/conf.d/90-api-platform.ini

# We use prod env in order to hit app.php instead of app_dev.php
# The switch between dev and prod is done by the env var SYMFONY_ENV.
# (anything different than "dev" is considered "prod")
CMD ["/app/docker/run.sh"]
