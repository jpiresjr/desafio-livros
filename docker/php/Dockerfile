FROM php:8.4-fpm

# Dependências de sistema necessárias
RUN apt-get update && apt-get install -y \
    git curl zip unzip libicu-dev libzip-dev libpng-dev zlib1g-dev libonig-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && sed -ri 's/^;?clear_env\s*=.*/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 8000

CMD ["php-fpm"]