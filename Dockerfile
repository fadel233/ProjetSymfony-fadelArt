# ========= PROD =========
FROM base AS prod

WORKDIR /app/app

ENV APP_ENV=prod
ENV APP_DEBUG=0

COPY app/ /app/app

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN php bin/console cache:clear \
 && php bin/console cache:warmup

EXPOSE 8080

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
