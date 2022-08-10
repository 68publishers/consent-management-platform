FROM 68publishers/docker-images:php-nginx-unit-7.4 AS app

MAINTAINER support@68publishers.io

########################################################################################################################
FROM postgres:13.6-alpine AS db
