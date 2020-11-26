# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG NODE_VERSION=14

# "common" stage
FROM node:${NODE_VERSION}-alpine AS api_platform_client_common

EXPOSE 3000

WORKDIR /usr/src/client

RUN yarn global add @api-platform/client-generator

# prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN set -eux; \
	yarn; \
	npx next telemetry disable

COPY . ./

VOLUME /usr/src/client/node_modules


# "development" stage
# depends on the "common" stage above
FROM api_platform_client_common AS api_platform_client_development

CMD ["yarn", "dev:https"]


# "build" stage
# depends on the "common" stage above
FROM api_platform_client_common AS api_platform_client_build

ENV NODE_ENV production
ARG REACT_APP_API_ENTRYPOINT

RUN set -eux; \
	yarn build

CMD ["yarn", "start:https"]
