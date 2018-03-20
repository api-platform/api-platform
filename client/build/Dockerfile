FROM node:9.4-alpine

RUN mkdir -p /usr/src/client

WORKDIR /usr/src/client

RUN yarn global add @api-platform/client-generator

# Prevent the reinstallation of node modules at every changes in the source code
COPY source/package.json source/yarn.lock ./
RUN yarn install

COPY source/. ./

CMD yarn start
