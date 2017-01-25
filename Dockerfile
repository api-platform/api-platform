FROM php:7.1-apache

# PHP extensions
ENV APCU_VERSION 5.1.7
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

# Apache config
RUN a2enmod rewrite
ADD docker/apache/envvars /etc/apache2/envvars
ADD docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# PHP config
ADD docker/php/php.ini /usr/local/etc/php/php.ini

# Install Git
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
    && rm -rf /var/lib/apt/lists/*

# Add the application
ADD . /var/www/app
WORKDIR /var/www/app

# Install composer
RUN ./docker/composer.sh \
    && mv composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

# Prepare directories with correct permissions
RUN (rm -rf var || true)
RUN mkdir -p var/cache var/logs var/sessions
RUN chown -R www-data:www-data /var/www

CMD ["/app/docker/start.sh"]
