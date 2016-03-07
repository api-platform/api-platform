FROM php:7.0-apache

# PHP extensions
RUN docker-php-ext-install mbstring

# Apache & PHP configuration
RUN a2enmod rewrite
ADD docker/apache/vhost.conf /etc/apache2/sites-enabled/default.conf
ADD docker/php/php.ini /usr/local/etc/php/php.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/bin/composer

# Add the application
ADD . /app
WORKDIR /app

# Remove cache and logs if some and fixes permissions
RUN ((rm -rf var/cache/* && rm -rf var/logs/* && rm -rf var/sessions/*) || true) \

    # Install dependencies
    && composer install -o && app/console cache:warmup -e=prod \

    # Fixes permissions issues in non-dev mode
    && chown -R www-data . var/cache var/logs var/sessions

CMD ["/app/docker/apache/run.sh"]
