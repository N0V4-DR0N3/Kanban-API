FROM inovedados/php:8.3-apache

USER root

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install nodejs -y \
    && node -v \
    && npm -v

RUN npm i -g bun && bun -v

USER php

COPY --chown=php:php . /var/www/html

# Install dependencies
RUN composer install
#
#RUN bun i && \
#     bun run build
