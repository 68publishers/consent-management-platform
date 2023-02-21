FROM 68publishers/docker-images:php-nginx-unit-7.4 AS app

########################################################################################################################
FROM 68publishers/docker-images:php-nginx-unit-7.4 AS worker

RUN apk add --update --no-cache --allow-untrusted supervisor
RUN mkdir -p "/etc/supervisor/logs"

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

########################################################################################################################
FROM postgres:13.6-alpine AS db
