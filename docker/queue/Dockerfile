FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y
# Install extensions for php
RUN docker-php-ext-install pdo pdo_mysql

COPY start.sh /usr/local/bin/start

RUN chmod u+x /usr/local/bin/start

CMD ["/usr/local/bin/start"]