#!/bin/sh

cd /etc/nginx/certs || exit

for domain in ${RENEWED_DOMAINS}; do
	domain="${domain#\*.}"
	cp --remove-destination "${RENEWED_LINEAGE}/fullchain.pem" "${domain}.crt"
	cp --remove-destination "${RENEWED_LINEAGE}/privkey.pem" "${domain}.key"
	cp --remove-destination "${RENEWED_LINEAGE}/chain.pem" "${domain}.chain.pem"
	chown "$CERTS_USER_OWNER:$CERTS_GROUP_OWNER" "${domain}".*
	chmod "$CERTS_FILES_MODE" "${domain}".*
done
