FROM 68publishers/docker-images:php-nginx-unit-7.4 AS app

MAINTAINER support@68publishers.io

########################################################################################################################
FROM postgres:13.6-alpine AS db

########################################################################################################################
FROM 68publishers/docker-images:php-nginx-unit-7.4 AS worker

RUN apk add --update --no-cache --allow-untrusted supervisor
RUN mkdir -p "/etc/supervisor/logs"

COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
