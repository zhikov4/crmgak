FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE $PORT

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
