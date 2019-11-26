Installation instructions
#########################

.. contents::

Step 0: Dependencies
====================

To install PhpReport in your system, you will need the following software:

* PostgreSQL database server (tested with PostgreSQL 9)

* PHP 7.0 or higher

  * Support for PostgreSQL

* Web server (tested with Apache 2.x)

  * PHP module

Installing dependencies on selected GNU/Linux distros
-----------------------------------------------------

Run the following command with root privileges:

* Debian, Ubuntu: ``apt-get install postgresql apache2 php php-pgsql php-xml``

* Fedora: ``dnf install postgresql-server httpd php php-pgsql php-xml``

* RHEL: ``yum install postgresql-server httpd php php-pgsql php-xml``

Step 1: Setting up the database
===============================

Connect to your database server as superuser, or a user with enough
permissions to create databases and users.

Run the following command with root permissions::

  su postgres -c psql

Once you have logged into PostgreSQL shell, run the following commands
to create a database, a user and grant enough permissions (change the
names and passwords if you feel like to, but remember it in the next steps)::

  CREATE DATABASE phpreport;
  CREATE USER phpreport WITH PASSWORD 'phpreport';
  ALTER DATABASE phpreport OWNER TO phpreport;

Step 2: Setting up the files
============================

Move the program files to a location available for the web server, inside
a directory called phpreport. Usual default locations for web servers are:

* On Debian, Ubuntu: ``/var/www/``
* On Fedora, RHEL: ``/var/www/html/``

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
instructions. If you didn't change the names and passwords specified in step 1,
you won't need to modify the default values.

Manual setup of schema and initial data
---------------------------------------

Follow these steps only if you haven't used the installation wizard. In first
place, browse to the ``phpreport`` directory and
create a configuration file with the default options with the command::

  cp config/config.defaults config/config.php

If you changed any of data on the first step, edit the file ``config/config.php``
and set the correct values for the DB name, user, password, host, etc.

Browse to the directory ``sql/`` and run the following commands (you will be
asked by the password interactively, default is ``phpreport``)::

  psql -h localhost -W -U phpreport phpreport < schema.sql
  psql -h localhost -W -U phpreport phpreport < uniqueConstraints.sql
  psql -h localhost -W -U phpreport phpreport < otherConstraints.sql
  psql -h localhost -W -U phpreport phpreport < initialData.sql
  psql -h localhost -W -U phpreport phpreport < update/all.sql

Set the correct DB name, user and host if they are different.

Step 4: Try the application
===========================

Use a browser to open the correct URL of your web server, and use the user name
``admin`` and the password ``admin`` for the first time. You will be able to
create more users and
change their passwords once you enter the application.

Step 5: remove dangerous files
==============================

Once the installation is complete and you have checked it is working, remove the
``install/`` and ``update/`` directories inside your PhpReport, to prevent other
users from resetting your DB.

Step 6: last configuration bits
===============================

You might have to modify some parameters in the file `config/config.php` to
match your work domain. In particular, you might have to modify the default
total number of holiday hours for a full-time worker. It is specified by the
`YEARLY_HOLIDAY_HOURS` attribute, and you have to modify the following line to
change it::

   * @name YEARLY_HOLIDAY_HOURS
   * @global int holiday hours per year for an 8-hour working journey
   */
  define ('YEARLY_HOLIDAY_HOURS', 184);

Remember to check the `data model for holiday management
<../user/overview.rst#data-model-for-holiday-management>`__ to know how it
works and which value you should put there.
