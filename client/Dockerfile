# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NODE_VERSION=13
ARG NGINX_VERSION=1.17


# "common" stage
FROM node:${NODE_VERSION}-alpine AS api_platform_client_common

EXPOSE 3000

WORKDIR /usr/src/client

RUN npm i -g @api-platform/client-generator

# prevent the reinstallation of node modules at every changes in the source code
COPY package.json package-lock.json ./
COPY . ./

RUN set -eux; \
	npm install --unsafe-perm; \
	npx next telemetry disable

VOLUME /usr/src/client/node_modules

ENV HTTPS true


# "development" stage
# depends on the "common" stage above
FROM api_platform_client_common AS api_platform_client_development

CMD ["npm", "run", "dev:ssl", "--unsafe-perm"]


# "build" stage
# depends on the "common" stage above
FROM api_platform_client_common AS api_platform_client_build

ENV NODE_ENV production
ARG REACT_APP_API_ENTRYPOINT

RUN set -eux; \
	npm run build

CMD ["npm", "run", "start:ssl"]


# "nginx" stage
# depends on the "build" stage above
FROM nginx:${NGINX_VERSION}-alpine AS api_platform_client_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /usr/src/client/build

COPY --from=api_platform_client_build /usr/src/client/build ./
