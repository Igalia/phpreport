How to set up LDAP authentication
#################################

The implementation of LDAP authentication connectors is robust, but its
configuration is a bit "hackish". In any case, if you want to go on you need to
edit the file `config/config.php` as explained below.

As a precondition, you will need an extra dependency in your system:

* Debian: php-ldap
* Ubuntu/Fedora/RHEL: php-ldap

First, locate the following block of lines and edit them according to your LDAP
server settings::

  // LDAP connection configuration

  /**
   * @name LDAP_SERVER
   * @global string LDAP server ip or name
   */
  define('LDAP_SERVER', 'localhost');

  /**
   * @name LDAP_PORT
   * @global int LDAP server port
   */
  define('LDAP_PORT', 389);

  /**
   * @name LDAP_BASE
   * @global string LDAP base OIDs
   */
  define('LDAP_BASE', 'dc=igalia,dc=com');

After that, locate the following block of lines and uncomment them to enable
the code that implements LDAP authentication in PhpReport (don't modify the
content of the lines, only uncomment them)::

  // uncomment the next lines to enable LDAP user authentication

  /**
   * @name USER_DAO
   * @global string User DAO implementation used
   */
  define('USER_DAO', 'HybridUserDAO');
  /**
   * @name USER_GROUP_DAO
   * @global string User Group DAO implementation used
   */
  define('USER_GROUP_DAO', 'LDAPUserGroupDAO');
  /**
   * @name BELONGS_DAO
   * @global string Belongs DAO implementation used
   */
  define('BELONGS_DAO', 'LDAPBelongsDAO');

  // end lines for LDAP user authentication

You might need to modify two more lines at `config/config.php` to match your
LDAP group structure. The following code indicates which is the name of the most
basic group of users, the one that contains all the members that will use
PhpReport::

  /**
   * @name ALL_USERS_GROUP
   * @global string users group used for retrieving all users
   */
  define('ALL_USERS_GROUP', 'staff');

You can also define a group to display only active employees based on
its respective LDAP group::

  /**
   * @name EMPLOYEES_GROUP
   * @global string users group used for retrieving all active employees
   */
  define('EMPLOYEES_GROUP', 'employees');


And in this line you must indicate a list with the names of the user groups that
will have some meaning for PhpReport::

  /**
   * @name USER_GROUPS
   * @global string all user groups for displaying on the interfaces as
   * a serialized array, compulsory when using LDAP user authentication
   */
  define('USER_GROUPS', serialize(array('staff', 'admin')));

Once you have set the groups up, you can specify the pages and web services each
group is allowed to enter modifying the file `config/permission.php`. Usually
you would like to have a *staff* and an *admin* group so you would only need to
modify that file to replace the default group names with the actual ones.

Finally, take into account that users must still exist both in your database and
your LDAP server, and in this state of the configuration you cannot log into
PhpReport because users don't fulfill both conditions. You should log into your
database and add users manually with the following commands::

  psql -h localhost -W -U phpreport phpreport

  insert into usr(login) values ('username1');
  insert into usr(login) values ('username2');
  etc.
