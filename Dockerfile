FROM 68publishers/docker-images:php-nginx-unit-7.4 AS app

RUN apk add --no-cache --update jq=~1.6

########################################################################################################################
FROM postgres:13.6-alpine AS db
