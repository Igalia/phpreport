# Installation instructions

## Step 0: Dependencies

To install PhpReport in your system, you will need the following
software:

- PostgreSQL database server (tested with PostgreSQL 9)
- PHP 7.3 or higher
  - Support for PDO and PostgreSQL
- Web server (tested with Apache 2.x)
  - PHP module
- Node.js with a version of 20 or higher
- NPM
- Python with a version of 3.11 or higher
- PIP
- The Alembic package for Python

### Installing dependencies on selected GNU/Linux distros

Run the following command with root privileges:

- Ubuntu:
  `apt install postgresql apache2 php php-pgsql php-xml php-pdo nodejs npm python3`
- Debian: `apt install postgresql apache2 php php-pgsql php-xml nodejs npm python3`
- Fedora:
  `dnf install postgresql-server httpd php php-pgsql php-xml php-pdo nodejs npm python3`

If you are installing PhpReport from sources instead of a release
package, you must install composer to manage the project dependencies.
Follow the official docs for the instructions:
<https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos>

## Step 1: Setting up the database

Connect to your database server as superuser, or a user with enough
permissions to create databases and users.

Run the following command with root permissions:

    su postgres -c psql

Once you have logged into PostgreSQL shell, run the following commands
to create a database, a user and grant enough permissions (change the
names and passwords if you feel like to, but remember it in the next
steps):

    CREATE DATABASE phpreport;
    CREATE USER phpreport WITH PASSWORD 'phpreport';
    ALTER DATABASE phpreport OWNER TO phpreport;

## Step 2: Setting up the files

Move the program files to a location available for the web server,
inside a directory called phpreport. The usual default location for the
Apache web server is: `/var/www/html/`

If you are installing PhpReport from sources instead of a release
package, you must also run `composer dump-autoload -o` inside PhpReport
root directory, so it can generate the autoload files. Then, run
`composer install` to install the project dependencies.

## Step 3: Creating the schema and initial data of the database

Now that Alembic has been set up in the project, database setup can be handled with it. Please see the section in the [api documentation](../developer/api.md) dealing with Alembic and running the migrations.

### Legacy methods

You have two ways to do it: using the included [web installation
wizard](#installation-wizard) or
[manually](#manual-setup-of-schema-and-initial-data).

#### Installation wizard

As a precondition, your web server has to have write permissions on the
config directory of PhpReport to write the configuration file. Once that
is done, open the url
`http://your-web-server/phpreport/install/index.php`, and follow the
on-screen instructions. If you didn't change the names and passwords
specified in step 1, you won't need to modify the default values.

#### Manual setup of schema and initial data

Follow these steps only if you haven't used the installation wizard. In
first place, browse to the `phpreport` directory and create a
configuration file with the default options with the command:

    cp .env.example .env
    cp frontend/.env.example frontend/.env

If you changed any of data on the first step, edit the file
`.env` and set the correct values for the DB name, user,
password, host, etc.

Browse to the directory `sql/` and run the following commands (you will
be asked by the password interactively, default is `phpreport`):

    psql -h localhost -W -U phpreport phpreport < schema.sql
    psql -h localhost -W -U phpreport phpreport < uniqueConstraints.sql
    psql -h localhost -W -U phpreport phpreport < otherConstraints.sql
    psql -h localhost -W -U phpreport phpreport < initialData.sql
    psql -h localhost -W -U phpreport phpreport < update/all.sql

Set the correct DB name, user and host if they are different.

## Step 4: Try the application

Use a browser to open the correct URL of your web server, and use the
user name `admin` and the password `admin` for the first time. You will
be able to create more users and change their passwords once you enter
the application.

## Step 5: Remove dangerous files

Once the installation is complete and you have checked it is working,
remove the `install/` and `update/` directories inside your PhpReport,
to prevent other users from resetting your DB.

## Step 6: Last configuration bits

You might have to modify some parameters in the file
[.env] to match your work domain. In
particular, you might have to modify the default total number of holiday
hours for a full-time worker. It is specified by the
[YEARLY_HOLIDAY_HOURS] attribute, and you have to modify the
following line to change it:

```
YEARLY_HOLIDAY_HOURS=184
```

Remember to check the [data model for holiday
management](../user/overview.md#data-model-for-holiday-management) to
know how it works and which value you should put there.
