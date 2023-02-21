# PROD
FROM 68publishers/cmp:latest AS worker

RUN apk add --update --no-cache --allow-untrusted supervisor

COPY --chown=1000:1000 docker/worker/logs /etc/supervisor/logs
COPY --chown=1000:1000 docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]
