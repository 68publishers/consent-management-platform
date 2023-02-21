# PROD
FROM 68publishers/docker-images:php-nginx-unit-7.4 AS app

MAINTAINER support@68publishers.io

COPY --chown=1000:1000 bin /app/phpfpm/bin
COPY --chown=1000:1000 config /app/phpfpm/config
COPY --chown=1000:1000 public /app/phpfpm/public
COPY --chown=1000:1000 src /app/phpfpm/src
COPY --chown=1000:1000 translations /app/phpfpm/translations
COPY --chown=1000:1000 var /app/phpfpm/var
COPY --chown=1000:1000 vendor /app/phpfpm/vendor
COPY --chown=1000:1000 assets /app/phpfpm/assets

# Todo: Replace the command with env dump. See: https://github.com/68publishers/environment#env-variables-cachingdumping
RUN echo "<?php return ['APP_ENV' => 'prod'];" > /app/phpfpm/.env.local.php
RUN ln -s /app/phpfpm/public /app/phpfpm/www

CMD ["run-nginx-unit"]
