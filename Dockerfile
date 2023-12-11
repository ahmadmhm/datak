FROM alpine:3.19

ARG ALPINE_VERSION=3.19

LABEL Maintainer="Ahmad Mohammadi <ahmadmohammadi940@gmail.com>" \
      Description="Lightweight container with Nginx 1.24 based on Alpine Linux."

RUN echo https://mirrors.pardisco.co/alpine/v$ALPINE_VERSION/main > /etc/apk/repositories
RUN echo https://mirrors.pardisco.co/alpine/v$ALPINE_VERSION/community >> /etc/apk/repositories

# Install packages and remove default server definition
RUN apk add --no-cache php83 \
    php83-common \
    php83-fpm \
    php83-pdo \
    php83-opcache \
    php83-zip \
    php83-phar \
    php83-iconv \
    php83-cli \
    php83-curl \
    php83-openssl \
    php83-mbstring \
    php83-tokenizer \
    php83-fileinfo \
    php83-json \
    php83-xml \
    php83-xmlwriter \
    php83-xmlreader \
    php83-simplexml \
    php83-dom \
    php83-pdo_pgsql \
    php83-pdo_mysql \
    php83-pdo_sqlite \
    php83-pecl-redis \
    php83-posix \
    php83-pcntl \
    php83-bcmath \
    php83-ctype \
    php83-gmp \
    php83-gd \
    php83-zlib \
    php83-intl \
    php83-ctype \
    php83-exif \
    php83-soap \
    php83-sockets

RUN apk add --no-cache nginx \
    supervisor \
    curl \
    tzdata \
    nano \
    git \
    vim \
    htop

RUN ln -sf /usr/bin/php83 /usr/bin/php

# Install PHP tools
COPY --from=composer:2.7.7 /usr/bin/composer /usr/local/bin/composer

# Configure nginx
COPY .docker/prod/config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY .docker/prod/config/fpm-pool.conf /etc/php83/php-fpm.d/www.conf
COPY .docker/prod/config/php.ini /etc/php83/conf.d/custom.ini

# Configure supervisord
COPY .docker/prod/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN set -x \
	&& adduser -u 1000 -D -S -G www-data www-data

# Setup document root
RUN mkdir -p /var/www/html

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R www-data.www-data /var/www/html && \
  chown -R www-data.www-data /run && \
  chown -R www-data.www-data /var/lib/nginx && \
  chown -R www-data.www-data /var/log/nginx

# Install supercronic
RUN curl -o /usr/local/bin/supercronic -L https://github.com/aptible/supercronic/releases/latest/download/supercronic-linux-amd64 && \
    chmod +x /usr/local/bin/supercronic

# Switch to use a non-root user from here on
USER www-data

# Add application
WORKDIR /var/www/html
COPY --chown=www-data ./ /var/www/html/

RUN chmod 777 -R storage/ \
 && chmod 777 -R bootstrap/cache/ \
 && chmod 755 .docker/prod/docker-entrypoint.sh \
 && touch phpunit-report.xml phpunit-coverage.xml \
 && chmod 777 phpunit-report.xml phpunit-coverage.xml \
 && touch database/database_test.sqlite

RUN composer install --no-autoloader

EXPOSE 8080

ENTRYPOINT ["./.docker/prod/docker-entrypoint.sh"]
