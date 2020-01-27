FROM php:7.3-cli

RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o /php-cs-fixer

COPY "entrypoint.sh" "/entrypoint.sh"

RUN chmod +x /entrypoint.sh && chmod a+x /php-cs-fixer
ENTRYPOINT ["/entrypoint.sh"]
