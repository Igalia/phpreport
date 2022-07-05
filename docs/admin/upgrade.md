Upgrade instructions
====================

::: {.contents}
:::

From version 2.20 to 2.21
-------------------------

Unpack the files of PhpReport 2.21 at the same location as the original
files, overwriting the existing contents. After that, run the upgrade
script located at the directory `update/`:

    cd update
    php -f update-from-2.20-to-2.21.php

Alternatively, you can open the following URL in your browser to run the
script:

    http://your-web-server/phpreport/update/update-from-2.20-to-2.21.php

This upgrade also adds several new values to `config/config.php`. Open
the file `config/config.template` and look for the message \"New from
PhpReport 2.21\". Copy the configuration parameters below that point to
your own `config.php` and customize them if necessary.

Once the process is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.19 to 2.20
-------------------------

Unpack the files of PhpReport 2.20 at the same location as the original
files, overwriting the existing contents.

There are no scripts to run or additional steps to be done in this
upgrade. Just make sure to remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.18 to 2.19
-------------------------

Unpack the files of PhpReport 2.19 at the same location of the original
files, overwriting the existing contents.

There are no scripts to be run in this upgrade, but several new values
were added to `config/config.php`. Open the file
`config/config.template` and look for the message \"New from PhpReport
2.19\". Copy the configuration parameters below that point to your own
`config.php` and customize them if necessary.

Once the process is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.17 to 2.18
-------------------------

Unpack the files of PhpReport 2.18 at the same location of the original
files, overwriting the existing contents. After that run the upgrade
script located at the directory `update/`:

    cd update
    php -f update-from-2.17-to-2.18.php

Alternatively, you can open the following URL in your browser to run the
script:

    http://your-web-server/phpreport/update/update-from-2.17-to-2.18.php

This upgrade adds several new values to `config/config.php`. Open the
file `config/config.template` and look for the message \"New from
PhpReport 2.18\". Copy the configuration parameters below that point to
your own `config.php` and customize them if necessary.

Once the process is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.16 to 2.17
-------------------------

Unpack the files of PhpReport 2.17 at the same location of the original
files, overwriting the existing contents. After that run the upgrade
script located at the directory `update/`:

    cd update
    php -f update-from-2.16-to-2.17.php

Alternatively, you can open the following URL in your browser to run the
script:

    http://your-web-server/phpreport/update/update-from-2.16-to-2.17.php

This upgrade will make task templates persistent. These used to be
stored as browser cookies and, therefore, should be migrated by every
individual user. Users willing to migrate their old templates must open
this URL once:

    http://your-web-server/phpreport/web/migrate-templates.php

Also take into account this upgrade simplifies the relation between
projects and customers. Any project assigned to more than one customer
will be reassigned to the most frequent customer. In case the default
assignment does not suit for you, you can use the old DB values to
fine-tune the migration: the table `requests` contains the relations
between customers and projects before the migration, and the field
`customerid` in the `tasks` table indicates the customer every task was
assigned to.

Finally, this upgrade also adds a new permission level called
\"manager\". Standard users will see access to certain reports
restricted, you need to decide which of your users require access to all
reports and give them the \"manager\" role.

Once the process is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.1 to 2.16
------------------------

Unpack the files of PhpReport 2.16 at the same location of the original
files, overwriting the existing contents. After that run the upgrade
script located at the directory `update/`:

    cd update
    php -f update-from-2.1-to-2.16.php

Alternatively, you can open the following URL in your browser to run the
script:

    http://your-web-server/phpreport/update/update-from-2.1-to-2.16.php

Once the upgrade is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

From version 2.0 to 2.1
-----------------------

Unpack the files of PhpReport 2.1 at the same location of the original
files, overwriting the existing contents. After that run the upgrade
script located at the directory `update/`:

    cd update
    php -f update-from-2.0-to-2.1.php

Alternatively, you can open the following URL in your browser to run the
script:

    http://your-web-server/phpreport/update/update-from-2.0-to-2.1.php

Once the upgrade is complete, remove the directories `update/` and
`install/` inside your PhpReport, to prevent other users from altering
your DB.

Between any 2.x versions
------------------------

You can migrate between any 2.x releases by unpacking the files of the
latest release at the same location of the original files, overwriting
the existing contents, and then running the upgrade scripts in order.
For example, if you are migrating from 2.1 to 2.17:

    cd update
    php -f update-from-2.1-to-2.16.php
    php -f update-from-2.16-to-2.17.php

Please, also read carefully the documentation about every individual
step in the sections above. You may also have to update your
`config/config.php` file as explained there.

Remember to remove the directories `update/` and `install/` inside your
PhpReport when the migration is done.

From version 1.x to 2.0
-----------------------

PhpReport 2.0 is a completely new application written from scratch. We
have provided a DB upgrade script but the migration is a bit tricky and
results are not guaranteed. For these reasons, you will have to install
PhpReport 2.0 in a new location and create a new DB for it which needs
to be available side by side with PhpReport 1.x database during the
migration.

Begin following the [installation instructions](installation.md) from
steps 0 to 2. Take into account you will probably have to use a
different name for the DB and extract the files to a different directory
to avoid conflicts with the existing installation. At step 3 you must
follow the manual setup instructions, but you must not run the command
that adds `initialData.sql` to the DB.

Now you have to run the script from the command line, indicating the
host, port, database name, user and password for both the 1.x database
(source) and 2.0 database (destination):

    cd update
    php -f migration.php source-host source-port source-db source-db-user
          source-db-password destination-host destination-port
          destination-db destination-db-user destination-db-password

During the process you might be asked some questions to resolve
migration conflicts. Finally, when the process is complete, test the
application, and remember to remove the directories `update/` and
`install/` to prevent other users from altering your DB.
