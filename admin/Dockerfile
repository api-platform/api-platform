# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NODE_VERSION=13
ARG NGINX_VERSION=1.17


# "development" stage
FROM node:${NODE_VERSION}-alpine AS api_platform_admin_development

WORKDIR /usr/src/admin

# prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN set -eux; \
	apk add --no-cache --virtual .gyp \
		g++ \
		make \
		python \
	; \
	yarn install; \
	apk del .gyp

COPY . ./

VOLUME /usr/src/admin/node_modules

ENV HTTPS true

CMD ["yarn", "start"]


# "build" stage
# depends on the "development" stage above
FROM api_platform_admin_development AS api_platform_admin_build

ARG REACT_APP_API_ENTRYPOINT

RUN set -eux; \
	yarn build


# "nginx" stage
# depends on the "build" stage above
FROM nginx:${NGINX_VERSION}-alpine AS api_platform_admin_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/src/admin/build

COPY --from=api_platform_admin_build /usr/src/admin/build ./
