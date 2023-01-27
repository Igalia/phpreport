FROM debian:bullseye-slim

RUN apt-get update
RUN apt-get -y install php php-cli php-pgsql php-xml php-curl composer

WORKDIR /app

COPY . /app

COPY config/config.test.php /app/config/config.php

RUN composer update
RUN composer install
RUN composer dump-autoload -o

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
