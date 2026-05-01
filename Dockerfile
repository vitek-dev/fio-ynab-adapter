FROM thecodingmachine/php:8.5-v5-slim-cli

ENV TEMPLATE_PHP_INI=production \
    PHP_EXTENSION_MBSTRING=1 \
    PHP_INI_MEMORY_LIMIT=128M \
    PHP_INI_OPCACHE_ENABLE=1 \
    PHP_INI_OPCACHE_VALIDATE_TIMESTAMPS=0

ENV FIO_API_TOKEN="" \
    YNAB_API_TOKEN="" \
    YNAB_BUDGET_ID="" \
    YNAB_ACCOUNT_ID=""

WORKDIR /usr/src/app

COPY --chown=docker:docker bin ./bin
COPY --chown=docker:docker config ./config
COPY --chown=docker:docker src ./src
COPY --chown=docker:docker composer.json composer.lock* ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --prefer-dist \
        --optimize-autoloader \
        --classmap-authoritative
