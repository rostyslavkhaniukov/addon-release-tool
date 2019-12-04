FROM pdffiller/php71-ubuntu16:v1.2.0

ARG BUILD_ID=0
ARG VERSION=0.0.1

ENV BUILD_ID=${BUILD_ID} \
    APPLICATION_VERSION=${VERSION} \
    DEBIAN_FRONTEND=noninteractive \
    SERVICE_ROLE=web,cron,worker \
    NUMPROCS_WORKER=1 \
    REGISTER_ADDON=1 \
    UPDATE_ADDON=1

COPY . /app

RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/cron

RUN cp -R /app/docker/config/nginx/* /etc/nginx/ \
    && mkdir -p /entrypoint.d \
    && cp -R /app/docker/provision/entrypoint.d/* /entrypoint.d/ \
    && cp -R /app/docker/config/supervisor/* /etc/supervisor/ \
    && cp -R /app/docker/config/cron/* /etc/cron.d/ \
    && mkdir -p /etc/php/7.1/conf.d \
    && cp /app/docker/config/php/app.ini /etc/php/7.1/conf.d/app.ini \
    && ln -s /etc/php/7.1/conf.d/app.ini /etc/php/7.1/cli/conf.d/999-app.ini \
    && ln -s /etc/php/7.1/conf.d/app.ini /etc/php/7.1/fpm/conf.d/999-app.ini

RUN bash /app/docker/provision/after-build.sh

EXPOSE 80

VOLUME ["/app/storage/logs", "/var/log/nginx", "/var/log/php"]

WORKDIR /app

CMD ["/app/docker/provision/supervisord.sh"]
