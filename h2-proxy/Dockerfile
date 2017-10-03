FROM nginx:1.13-alpine

RUN apk add --no-cache --virtual .persistent-deps openssl

RUN mkdir -p /etc/nginx/ssl/

RUN openssl req \
    -x509 \
    -nodes \
    -days 365 \
    -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/cert.key \
    -out /etc/nginx/ssl/cert.crt \
    -subj '/C=SS/ST=SS/L=Gotham City/O=API Platform Dev/CN=localhost'

COPY conf.d /etc/nginx/conf.d/
