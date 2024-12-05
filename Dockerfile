FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    curl \
    wget \
    nano

RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-scripts --no-interaction \
    && composer dump-autoload --no-interaction --optimize

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g yarn

RUN yarn install \
    && yarn build

RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

EXPOSE 9000

CMD ["php-fpm"]