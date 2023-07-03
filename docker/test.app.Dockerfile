FROM debian:bullseye-slim

RUN apt update
RUN apt -y install php php-cli php-pgsql php-xml php-curl composer python3 python3-pip

WORKDIR /app

COPY . /app
COPY .env.test /app/.env

RUN composer update
RUN composer install
RUN composer dump-autoload -o

RUN python3 -m pip install --upgrade pip
RUN pip install ./api
RUN pip install ./api[dev]

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
