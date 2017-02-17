FROM alpine:3.5

RUN apk add --no-cache varnish

COPY start.sh /usr/local/bin/docker-app-start

RUN chmod +x /usr/local/bin/docker-app-start

CMD ["docker-app-start"]
