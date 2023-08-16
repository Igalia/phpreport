FROM debian:trixie-slim

RUN apt update
RUN apt -y install php php-cli php-pgsql php-xml php-curl composer

WORKDIR /app

ADD composer.json /app

RUN composer update
RUN composer install
RUN composer dump-autoload -o

ADD . /app

# Remove other services files
RUN rm -rf app/api app/frontend

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
