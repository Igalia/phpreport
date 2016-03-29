Upgrade instructions
#########################

.. contents::

From version 2.1 to 2.16
========================

Unpack the files of PhpReport 2.16 at the same location of the original files,
overwriting the existing contents. After that run the upgrade script located at
the directory ``update/``::

  cd update
  php -f update-from-2.1-to-2.16.php

Alternatively, you can open the following URL in your browser to run the
script::

  http://your-web-server/phpreport/update/update-from-2.1-to-2.16.php

Once the upgrade is complete, remove the directories ``update/`` and ``install/``
inside your PhpReport, to prevent other users from altering your DB.

From version 2.0 to 2.1
=======================

Unpack the files of PhpReport 2.1 at the same location of the original files,
overwriting the existing contents. After that run the upgrade script located at
the directory ``update/``::

  cd update
  php -f update-from-2.0-to-2.1.php

Alternatively, you can open the following URL in your browser to run the
script::

  http://your-web-server/phpreport/update/update-from-2.0-to-2.1.php

Once the upgrade is complete, remove the directories ``update/`` and ``install/``
inside your PhpReport, to prevent other users from altering your DB.

.. WARNING ::

  Due to a `bug <https://trac.phpreport.igalia.com/ticket/191>`__ in the
  installation wizard, people who installed PhpReport using this method will
  find the following error during the upgrade process::

    Error updating database in step: .../remove-triggers-for-overlapping-control.sql
    Please consider doing a manual update

  If this is the only error message, you can safely ignore it: the migration has
  been completed successfully.

From version 1.x to 2.0
=======================

PhpReport 2.0 is a completely new application written from scratch. We have
provided a DB upgrade script but the migration is a bit tricky and results are
not guaranteed. For these reasons, you will have to install PhpReport 2.0 in a
new location and create a new DB for it which needs to be available side by side
with PhpReport 1.x database during the migration.

Begin following the `installation instructions <installation.rst>`__ from steps
0 to 2. Take into account you will probably have to use a different name for the
DB and extract the files to a different directory to avoid conflicts with the
existing installation. At step 3 you must follow the manual setup instructions,
but you must not run the command that adds ``initialData.sql`` to the DB.

Now you have to run the script from the command line, indicating the host, port,
database name, user and password for both the 1.x database (source) and 2.0
database (destination)::

  cd update
  php -f migration.php source-host source-port source-db source-db-user
		source-db-password destination-host destination-port
		destination-db destination-db-user destination-db-password

During the process you might be asked some questions to resolve migration
conflicts. Finally, when the process is complete, test the application, and
remember to remove the directories ``update/`` and ``install/`` to prevent other
users from altering your DB.
