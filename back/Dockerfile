FROM php:8.0-apache
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo_mysql
RUN apt update && apt install -y git
WORKDIR /var/www/html
COPY ./app /var/www/html
RUN composer update --ignore-platform-reqs
EXPOSE 80
