FROM php:8.2-cli-alpine

RUN apk add --no-cache postgresql-dev mariadb-connector-c-dev
RUN docker-php-ext-install pdo_mysql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer install --no-dev --optimize
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan db:seed --class=DatabaseSeeder --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
