FROM php:8.1-apache

# Install Mysql
RUN docker-php-ext-install pdo pdo_mysql
COPY ./ /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install unzip utility and libs needed by zip PHP extension 
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip

RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libsasl2-dev \
    libssl-dev

RUN pecl install memcached redis xdebug protobuf \
    && docker-php-ext-enable memcached opcache redis xdebug protobuf

# Configure OpenTelemetry extension
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
    opentelemetry

# SSL configuration
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb
RUN apt-get update && apt-get upgrade -y

# Ativa rewrite engine
RUN a2enmod rewrite

RUN chmod -R ugo+rw storage
RUN chmod o+w ./storage/ -R