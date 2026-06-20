FROM php:8.2-cli-alpine

RUN apk add --no-cache postgresql-dev mariadb-connector-c-dev
RUN docker-php-ext-install pdo_mysql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-sockets
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
RUN php artisan config:cache && php artisan route:cache

EXPOSE 8080

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --class=DatabaseSeeder --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
