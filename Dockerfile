FROM php:7.0-apache

# PHP extensions
ENV APCU_VERSION 5.1.5
RUN buildDeps=" \
        libicu-dev \
        zlib1g-dev \
    " \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        $buildDeps \
        libicu52 \
        zlib1g \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo_mysql \
        zip \
    && apt-get purge -y --auto-remove $buildDeps
RUN pecl install \
        apcu-$APCU_VERSION \
    && docker-php-ext-enable --ini-name 05-opcache.ini \
        opcache \
    && docker-php-ext-enable --ini-name 20-apcu.ini \
        apcu

# Apache & PHP configuration
RUN a2enmod rewrite
ADD docker/apache/vhost.conf /etc/apache2/sites-enabled/default.conf
ADD docker/php/php.ini /usr/local/etc/php/php.ini

# Install composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
    && rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/bin/composer

# Add the application
ADD . /app
WORKDIR /app

# Remove cache and logs if some and fixes permissions
RUN ((rm -rf var/cache/* && rm -rf var/logs/* && rm -rf var/sessions/*) || true) \
    # Fixes permissions issues in non-dev mode
    && chown -R www-data . var/cache var/logs var/sessions

CMD ["/app/docker/apache/run.sh"]
