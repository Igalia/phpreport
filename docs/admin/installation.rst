Installation instructions
#########################

.. contents::

Step 0: Dependencies
====================

To install PhpReport in your system, you will need the following software:

* PostgreSQL database server (tested with PostgreSQL 9)

* PHP 5.3.0 or higher

  * Support for PostgreSQL

* Web server (tested with Apache 2.2)

  * PHP module

Installing dependencies in Windows systems
------------------------------------------

You will have to download and install the software by yourself. Check each
program documentation for detailed instructions.

You can find Windows versions of the software in the following sites:

* PostgreSQL at http://www.postgresql.org/download/windows

* Apache 2.2 at http://www.apachelounge.com/download/. This is the recommended
  version you should install to use PHP 5.3. Follow instructions in ReadMe file.

* PHP 5.3 at http://windows.php.net/download/. Installation instructions are
  available here: http://www.sitepoint.com/how-to-install-php-on-windows.

Installing dependencies in Debian/Ubuntu systems
------------------------------------------------

Run the following command with root permission::

  apt-get install postgresql apache2 php5 php5-pgsql

Installing dependencies in Red Hat/Fedora systems
-------------------------------------------------

Run the following command with root permission::

  yum install postgresql-server httpd php php-pgsql php-xml

Step 1: Setting up the database
===============================

Connect to your database server as superuser, or a user with enough
permissions to create databases and users.

* On Windows: go to *Start Menu -> Programs -> PostgreSQL -> SQL Shell*, connect
  as the user ``postgres``.
* On GNU/Linux systems: run the following command with root permissions::

    su postgres -c psql

Once you have logged into PostgreSQL shell, run the following commands
to create a database, a user and grant enough permissions (change the
names and passwords if you feel like to, but remember it in the next steps)::

  CREATE DATABASE phpreport;
  CREATE USER phpreport WITH PASSWORD 'phpreport';
  ALTER DATABASE phpreport OWNER TO phpreport;

Step 2: Setting up the files
============================

Just move the program files to a location available for the web server, inside
a directory called phpreport. Usual default locations for web servers are:

* On Windows: ``C:\Apache2\htdocs\``
* On Debian, Ubuntu: ``/var/www/``
* On Red Hat, Fedora: ``/var/www/html/``

Step 3: Creating the schema and initial data of the database
============================================================

You have two ways to do it: using the included
`web installation wizard <#installation-wizard>`__ or
`manually <#manual-setup-of-schema-and-initial-data>`__.

Installation wizard
-------------------

As a precondition, your web server has to have write permissions on the config
directory of PhpReport to write the configuration file. Once that is done, open
the url ``http://your-web-server/phpreport/install/index.php``, and follow the
on-screen
instructions. If you didn't change the names and passwords specified on step 1,
defaults will just work fine.

Once the installation is complete, remove the ``install/`` directory inside your
PhpReport, to prevent other users to reset your DB again.

Manual setup of schema and initial data
---------------------------------------

Follow these steps only if you haven't used the installation wizard. Firstly,
create the configuration file with the default options with the command::

  cp config/config.defaults config/config.php

If you changed any of data on the first step, edit the file ``config/config.php``
and set the correct values for the DB name, user, password, host, etc.

Browse to the directory ``sql/`` and run the following commands (you will be
asked by the password (``phpreport``) interactively)::

  psql -h localhost -W -U phpreport phpreport < schema.sql
  psql -h localhost -W -U phpreport phpreport < uniqueConstraints.sql
  psql -h localhost -W -U phpreport phpreport < otherConstraints.sql
  psql -h localhost -W -U phpreport phpreport < initialData.sql
  psql -h localhost -W -U phpreport phpreport < triggers.sql

Set the correct DB name, user and host if they are different.

Step 4: Try the application
===========================

Use a browser to open the correct URL of your web server, and use the user name
``admin`` and the password ``admin`` for the first time. You will be able to
create more users and
change their passwords once you enter the application.
