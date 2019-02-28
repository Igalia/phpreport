# Go to http://localhost/phpreport to use it (username: admin ; password = admin)

FROM ubuntu:bionic

MAINTAINER Juan A. Suarez Romero <jasuarez@igalia.com>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update

RUN apt-get -y install postgresql apache2 php php-pgsql php-xml supervisor make docutils-common node-uglify

ADD . /var/www/html/phpreport/

WORKDIR  /var/www/html/phpreport/

RUN make help minify

RUN service postgresql start && su postgres -c psql < /var/www/html/phpreport/sql/create_db.sql

RUN service postgresql start && env PGPASSWORD='phpreport' psql -h localhost -U phpreport phpreport < /var/www/html/phpreport/sql/schema.sql

RUN service postgresql start && env PGPASSWORD='phpreport' psql -h localhost -U phpreport phpreport < /var/www/html/phpreport/sql/uniqueConstraints.sql

RUN service postgresql start && env PGPASSWORD='phpreport' psql -h localhost -U phpreport phpreport < /var/www/html/phpreport/sql/otherConstraints.sql

RUN service postgresql start && env PGPASSWORD='phpreport' psql -h localhost -U phpreport phpreport < /var/www/html/phpreport/sql/initialData.sql

RUN service postgresql start && env PGPASSWORD='phpreport' psql -h localhost -U phpreport phpreport < /var/www/html/phpreport/sql/update/all.sql

RUN ln -sf /var/www/html/phpreport/supervisor/supervisord.conf /etc/supervisor/conf.d/

RUN rm -fr /var/www/html/phpreport/install /var/www/html/phpreport/update

VOLUME /var/lib/postgresql/

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
