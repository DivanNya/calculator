# COMMON TOOLS
FROM alpine:3.18.4 as common-tools

COPY ./docker/common/wait-for /usr/local/bin/wait-for
RUN apk --no-cache add bash && chmod +x /usr/local/bin/wait-for

# PHP-Builder
FROM php:8.2-cli-alpine3.17 as php-builder

ARG USER='www-data'
ARG GROUP='www-data'

ENV COMPOSER_MEMORY_LIMIT -1

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN pear update-channels && pecl update-channels \
    && install-php-extensions @composer-2 opcache pdo_mysql

COPY ./docker/php/cli/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

USER ${USER}

WORKDIR /app

COPY --chown=${USER}:${GROUP} ./composer.json ./composer.lock /app/

RUN composer --no-ansi --no-interaction install --no-progress

COPY --chown=${USER}:${GROUP} ./tests/composer.json ./tests/composer.lock /tests/

RUN composer --no-ansi --no-interaction install --no-progress --working-dir=/tests

# PHP-FPM
FROM php:8.2-fpm-alpine3.17 as fpm

ARG USER='www-data'
ARG GROUP='www-data'
ARG ENV='production'

COPY ./docker/php/cli/conf.d/* $PHP_INI_DIR/conf.d
COPY ./docker/php/fpm/conf.d/extras.conf $PHP_INI_DIR/../php-fpm.d/extras.conf
COPY ./docker/php/fpm/conf.d/extras.ini $PHP_INI_DIR/conf.d/php-fpm.ini
COPY ./docker/php/php.ini-production $PHP_INI_DIR/php.ini

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions opcache pdo_mysql \
    && apk --no-cache add bash

RUN if [ "$ENV" = "development" ]; then \
    install-php-extensions @composer-2; \
fi

COPY --chown=${USER}:${GROUP} ./ /var/www/html
COPY --from=php-builder --chown=${USER}:${GROUP} /app /var/www/html
COPY --from=php-builder --chown=${USER}:${GROUP} /tests /var/www/html/tests

WORKDIR /var/www/html

USER ${USER}

CMD php-fpm
EXPOSE 9000

# NGINX
FROM nginx:1.20.2-alpine as nginx

COPY docker/nginx/conf.d/* /etc/nginx/conf.d

COPY --from=fpm /var/www/html/web /var/www/html/web

RUN apk update && apk upgrade && apk add --no-cache bash

COPY ./docker/nginx/conf.d/* /etc/nginx/conf.d
COPY ./docker/nginx/templates /etc/nginx/templates

EXPOSE 80

# FRONTEND SPA
FROM node:20.6.0-alpine3.18 as frontend-builder

ARG USER='node'
ARG GROUP='node'

RUN apk add python3 make g++ yarn

WORKDIR /app

USER ${USER}:${GROUP}

# SWAGGER
FROM swaggerapi/swagger-ui:v5.9.2 as swagger

ENV URL=/swagger-ui/spec.yml

ARG USER='nginx'
ARG GROUP='nginx'

USER ${USER}

COPY --chown=${USER}:${GROUP} ./swagger/* /usr/share/nginx/html