# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NODE_VERSION=14

# "common" stage
FROM node:${NODE_VERSION}-alpine AS api_platform_pwa_common

EXPOSE 3000

WORKDIR /usr/src/pwa

ENV NEXT_TELEMETRY_DISABLED 1

ENV YARN_VERSION=berry
ENV NODE_MODULES_CACHE=false
RUN yarn set version $YARN_VERSION

COPY . ./
RUN yarn

VOLUME /usr/src/pwa/.yarn


# "development" stage
# depends on the "common" stage above
FROM api_platform_pwa_common AS api_platform_pwa_dev

ENV API_PLATFORM_CLIENT_GENERATOR_OUTPUT .
RUN yarn add @api-platform/client-generator

CMD ["yarn", "dev"]


# "build" stage
# depends on the "common" stage above
FROM api_platform_pwa_common AS api_platform_pwa_prod

ENV NODE_ENV production
ARG NEXT_PUBLIC_ENTRYPOINT

RUN set -eux; \
	yarn build

CMD ["yarn", "start"]
