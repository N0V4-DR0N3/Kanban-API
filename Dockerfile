FROM websolusoficial/php:8.3-node20-puppetear

RUN docker-php-ext-configure intl && docker-php-ext-install intl && \
    docker-php-ext-install gettext

ENV PUPPETEER_SKIP_DOWNLOAD=true

RUN echo "max_execution_time=300\npost_max_size=500M\nmax_file_uploads=500\nupload_max_filesize=500M\nmemory_limit=1024M" > /usr/local/etc/php/conf.d/custom_params.ini

RUN apt update && apt install -y pdftk img2pdf qpdf

USER php

COPY --chown=php:php . /var/www/html

RUN composer install && \
    bun install

RUN echo "#!/bin/bash" > /var/www/html/entrypoint.sh \
&& echo "php artisan deploy --no-composer --no-seed" >> /var/www/html/entrypoint.sh \
    && echo 'echo "Starting supervisord... ENV ${APP_ENV}"' >> /var/www/html/entrypoint.sh \
    && echo "supervisord -n" >> /var/www/html/entrypoint.sh \
    && chmod +x /var/www/html/entrypoint.sh

ENTRYPOINT ["sh", "-c", "/var/www/html/entrypoint.sh"]
