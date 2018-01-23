FROM alpine:3.7

RUN apk add --no-cache varnish

COPY docker/varnish/conf /etc/varnish/
COPY docker/varnish/start.sh /usr/local/bin/docker-app-start

RUN chmod +x /usr/local/bin/docker-app-start

CMD ["docker-app-start"]
