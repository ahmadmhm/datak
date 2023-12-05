FROM alpine:3.18

ARG ALPINE_VERSION=3.18

LABEL Maintainer="Morteza Fathi <mortezaa.fathi@gmail.com>" \
      Description="Lightweight container with Nginx 1.24 based on Alpine Linux."

RUN echo https://mirrors.pardisco.co/alpine/v$ALPINE_VERSION/main > /etc/apk/repositories
RUN echo https://mirrors.pardisco.co/alpine/v$ALPINE_VERSION/community >> /etc/apk/repositories

# Install packages and remove default server definition
RUN apk add --no-cache php82 \
    php82-common \
    php82-fpm \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-json \
    php82-xml \
    php82-xmlwriter \
    php82-xmlreader \
    php82-simplexml \
    php82-dom \
    php82-pdo_pgsql \
    php82-pdo_mysql \
    php82-pdo_sqlite \
    php82-pecl-redis \
    php82-posix \
    php82-pcntl \
    php82-bcmath \
    php82-ctype \
    php82-gmp \
    php82-gd \
    php82-zlib \
    php82-intl \
    php82-ctype \
    php82-exif \
    php82-soap \
    php82-sockets

RUN apk add --no-cache nginx \
    supervisor \
    curl \
    tzdata \
    nano \
    git \
    vim \
    htop

RUN ln -s /usr/bin/php82 /usr/bin/php

# Install PHP tools
COPY --from=composer:2.6.5 /usr/bin/composer /usr/local/bin/composer

# Configure nginx
COPY .docker/prod/config/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY .docker/prod/config/fpm-pool.conf /etc/php82/php-fpm.d/www.conf
COPY .docker/prod/config/php.ini /etc/php82/conf.d/custom.ini

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
#RUN curl -o /usr/local/bin/supercronic -L https://github.com/aptible/supercronic/releases/latest/download/supercronic-linux-amd64 && \
#    chmod +x /usr/local/bin/supercronic

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
