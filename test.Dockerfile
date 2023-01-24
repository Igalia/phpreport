FROM debian:bullseye-slim

RUN apt-get update

RUN apt-get -y install php php-cli php-pgsql php-xml composer

WORKDIR /app

COPY . /app

COPY config/config_test.php /app/config/config.php

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
