FROM 68publishers/php:8.4-cli-prod-1.0.0 AS builder

WORKDIR /build
USER root

COPY . .

RUN set -ex \
    # clear ./var
    && rm -rf var/cache \
    && cd var/log/ && rm -rf $(ls | grep -v .gitkeep) && cd ../.. \
    && rm -rf var/git-version \
    # install composer (dev included)
    && composer install --no-interaction --no-ansi --prefer-dist --no-progress --dev \
    # export information about git repository
    && ./vendor/bin/tracy-git-version export-repository --output-file ./var/git-version/repository.json -vv \
    # dump .env.local.php file
    && composer dotenv:dump prod --empty \
    # install composer again without dev
    && composer install --no-interaction --no-ansi --prefer-dist --no-progress --optimize-autoloader --no-dev

FROM node:22.16.0-alpine3.22 AS assets-builder

WORKDIR /build
USER root

COPY . .

RUN set -ex \
    # clear ./public/dist
    && rm -rf public/dist \
    # install assets
    && yarn install --no-progress --non-interactive \
    && yarn run encore prod

FROM 68publishers/php:8.4-unit-prod-1.0.0 AS app

WORKDIR /var/www/html

COPY --from=assets-builder --chown=www-data:www-data /build/assets ./assets/
COPY --from=builder --chown=www-data:www-data /build/bin ./bin/
COPY --from=builder --chown=www-data:www-data /build/config ./config/
COPY --from=assets-builder --chown=www-data:www-data /build/public ./public/
COPY --from=builder --chown=www-data:www-data /build/src ./src/
COPY --from=builder --chown=www-data:www-data /build/translations ./translations/
COPY --from=builder --chown=www-data:www-data /build/var ./var/
COPY --from=builder --chown=www-data:www-data /build/vendor ./vendor/
COPY --from=builder --chown=www-data:www-data /build/composer* .
COPY --from=builder --chown=www-data:www-data /build/.env.local.php .

FROM 68publishers/php:8.4-cli-prod-1.0.0 AS worker

USER root

RUN apk add --update --no-cache supervisor \
    && mkdir -p /etc/supervisor/logs \
    && chown www-data:www-data /etc/supervisor/logs

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]

USER www-data

WORKDIR /var/www/html

COPY --from=assets-builder --chown=www-data:www-data /build/assets ./assets/
COPY --from=builder --chown=www-data:www-data /build/bin ./bin/
COPY --from=builder --chown=www-data:www-data /build/config ./config/
COPY --from=assets-builder --chown=www-data:www-data /build/public ./public/
COPY --from=builder --chown=www-data:www-data /build/src ./src/
COPY --from=builder --chown=www-data:www-data /build/translations ./translations/
COPY --from=builder --chown=www-data:www-data /build/var ./var/
COPY --from=builder --chown=www-data:www-data /build/vendor ./vendor/
COPY --from=builder --chown=www-data:www-data /build/composer* .
COPY --from=builder --chown=www-data:www-data /build/.env.local.php .
