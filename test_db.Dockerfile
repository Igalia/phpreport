FROM postgres:13.9

ENV POSTGRES_DB phpreport

# The files are run ordered by name, so rename them when copying
# to make sure they will be run correctly.
# See https://github.com/docker-library/docs/blob/master/postgres/README.md#initialization-scripts

COPY sql/schema.sql /docker-entrypoint-initdb.d/1.sql
COPY sql/uniqueConstraints.sql /docker-entrypoint-initdb.d/2.sql
COPY sql/otherConstraints.sql /docker-entrypoint-initdb.d/3.sql
COPY sql/initialData.sql /docker-entrypoint-initdb.d/4.sql
COPY sql/update/all.sql /docker-entrypoint-initdb.d/5.sql
