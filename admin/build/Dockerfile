FROM node:9.4-alpine

RUN mkdir -p /usr/src/admin

WORKDIR /usr/src/admin

# Prevent the reinstallation of node modules at every changes in the source code
COPY source/package.json source/yarn.lock ./
RUN yarn install

COPY source/. ./

CMD yarn start
