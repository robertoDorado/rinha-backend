#  PHP Drivers
FROM php:7.4-fpm
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Instalação do driver libpq-dev
RUN apt-get update \
    && apt-get install -y libpq-dev

# Permissão de super usuário para o composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Intalação do Composer
RUN apt-get install -y unzip \ 
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalação das dependências de desenvolvimento (incluindo o Composer)
COPY composer.json composer.lock /www/
RUN cd /www && composer install --no-scripts --no-autoloader
RUN cd /www && composer update --no-interaction

EXPOSE 9000