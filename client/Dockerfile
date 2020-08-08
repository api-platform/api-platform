# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NODE_VERSION=13
ARG NGINX_VERSION=1.17


# "development" stage
FROM node:${NODE_VERSION}-alpine AS api_platform_client_development

RUN yarn global add @api-platform/client-generator

# create and define the node_modules's cache directory
RUN mkdir -p /usr/src/cache
WORKDIR /usr/src/cache

# prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN set -eux; \
	yarn install

WORKDIR /usr/src/client

COPY . ./

ENV HTTPS true

COPY docker/node/start-cmd.sh /usr/local/bin/start-cmd
RUN chmod +x /usr/local/bin/start-cmd

CMD ["start-cmd"]


# "build" stage
# depends on the "development" stage above
FROM api_platform_client_development AS api_platform_client_build

ARG REACT_APP_API_ENTRYPOINT

RUN set -eux; \
	yarn build


# "nginx" stage
# depends on the "build" stage above
FROM nginx:${NGINX_VERSION}-alpine AS api_platform_client_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/src/client/build

COPY --from=api_platform_client_build /usr/src/client/build ./
