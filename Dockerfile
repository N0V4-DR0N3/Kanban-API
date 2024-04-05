FROM inovedados/php:8.3-apache

USER php

COPY --chown=php:php . /var/www/html

# Install dependencies
RUN composer install
