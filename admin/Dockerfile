FROM node:9.4-alpine

RUN mkdir -p /usr/src/admin

WORKDIR /usr/src/admin

# Prevent the reinstallation of node modules at every changes in the source code
COPY package.json yarn.lock ./
RUN yarn install

COPY . ./

CMD yarn start
