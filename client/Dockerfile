FROM node:11.5-alpine

RUN mkdir -p /usr/src/client

WORKDIR /usr/src/client

RUN yarn global add @api-platform/client-generator

# Prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN yarn install

COPY . ./

CMD yarn start
