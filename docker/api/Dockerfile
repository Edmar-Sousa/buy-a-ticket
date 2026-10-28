FROM php:8.4-fpm

ENV timezone="America/Fortaleza"
WORKDIR /var/www/html


RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    libtool \
    tzdata \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*


RUN curl -fsSL https://github.com/php/pie/releases/latest/download/pie.phar \
    -o /usr/local/bin/pie \
    && chmod +x /usr/local/bin/pie


RUN pie install phalcon/cphalcon


RUN set -ex \
    && ln -sf /usr/share/zoneinfo/${timezone} /etc/localtime \
    && echo "${timezone}" > /etc/timezone

COPY . .

EXPOSE 9000

CMD ["php-fpm"]
