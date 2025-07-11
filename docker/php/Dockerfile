FROM php:7.4.9-fpm

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  git \
  default-mysql-client \
  zlib1g-dev \
  libzip-dev \
  unzip \
  && docker-php-ext-install pdo_mysql zip \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/

RUN apt update \
  && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip \
  && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www