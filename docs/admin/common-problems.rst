Common problems
###############

.. contents::

PostgreSQL service is running but installation wizard seems to be unable to connect to it
-----------------------------------------------------------------------------------------

Probably your PostgreSQL server isn't allowing incoming connections through the
network. Check the file ``pg_hba.conf`` (location in Fedora:
``/var/lib/pgsql/data/``, Debian/Ubuntu: ``/etc/postgresql/<version>/main/``).
You will probably need to add the following lines::

  # TYPE  DATABASE        USER            CIDR-ADDRESS            METHOD
  # IPv4 local connections:
  host    all             all             127.0.0.1/32            md5
  # IPv6 local connections:
  host    all             all             ::1/128                 md5

I can't start PostgreSQL server service in Fedora
-------------------------------------------------

Maybe you haven't set the storage files up. Type (as root)::

  service postgresql initdb

There's an error message: 'It is not safe to rely on the system's timezone settings'
------------------------------------------------------------------------------------

Edit the configuration file ``php.ini``, adding a line like the following one but
changing ``Europe/Madrid`` with your own time zone::

  date.timezone = Europe/Madrid

php.ini is usually located in:

* Debian, Ubuntu: ``/etc/php5/apache2/php.ini``
* Fedora, Red Hat: ``/etc/php.ini``
* Windows: ``C:\PHP\php.ini``

There are a lot of warning and error messages mixed with the content of the web application
-------------------------------------------------------------------------------------------

Disable error output in PHP settings. Edit the configuration file php.ini and
locate the parameter ``display_errors``, changing its value to ``off``::

  display_errors = Off
