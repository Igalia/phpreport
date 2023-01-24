<?php
/*
 * Copyright (C) 2023 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */

/** Configuration file
 *
 *  This file contains configuration parameters values.<br/><br/>Parameters configured:
 *  <ul>
 *  <li>Database connection configuration
 *  <ul>
 *  <li>Name of the DB</li>
 *  <li>User to acces the DB</li>
 *  <li>User's password</li>
 *  <li>Name or IP of the database server</li>
 *  <li>Port of the database server</li>
 *  </ul></li>
 *  <li>LDAP connection configuration
 *  <ul>
 *  <li>Name or IP of the database server</li>
 *  <li>Port of the database server</li>
 *  <li>Base OIDs of the LDAP</li>
 *  </ul></li>
 *  <li>Generic DAOs configuration</li>
 *  <li>Specific DAOs configuration</li>
 *  <li>Plugin definitions</li>
 *  <li>Business rules definitions
 *  <ul>
 *  <li>Holiday hours per year for an 8-hour working journey</li>
 *  <li>Users group used for retrieving all users</li>
 *  </ul></li>
 *  </ul>
 *
 *
 * @filesource
 * @package PhpReport
 * @subpackage config
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */


// DB connection configuration

/**
 * @name DB_NAME
 * @global string DB name
 */
define('DB_NAME', 'phpreport');

/**
 * @name DB_USER
 * @global string DB user
 */
define('DB_USER', 'phpreport');

/**
 * @name DB_PASSWORD
 * @global string DB password
 */
define('DB_PASSWORD', 'phpreport');

/**
 * @name DB_HOST
 * @global string DB server IP or name
 */
define('DB_HOST', 'db');

/**
 * @name DB_PORT
 * @global int DB server port
 */
define('DB_PORT', 5432);


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

// DAO configuration

/**
 * @name DAO_BACKEND
 * @global string DAOs implementation used if no specific one is configured
 */
define('DAO_BACKEND', 'PostgreSQL');


/*
   Some standard DAOs can be replaced to add specific
   functionality to PhpReport
*/

// uncomment the next lines to enable LDAP user authentication

/**
 * @name USER_DAO
 * @global string User DAO implementation used
 */
//define('USER_DAO', 'HybridUserDAO');
/**
 * @name USER_GROUP_DAO
 * @global string User Group DAO implementation used
 */
//define('USER_GROUP_DAO', 'LDAPUserGroupDAO');
/**
 * @name BELONGS_DAO
 * @global string Belongs DAO implementation used
 */
//define('BELONGS_DAO', 'LDAPBelongsDAO');

// end lines for LDAP user authentication

/*
    Plugin definitions
*/
/**
 * @name CREATE_USER_POSTACTION
 * @global string plugin executed after creating a user
 */
//define('CREATE_USER_POSTACTION', 'EmailAdminPlugin');


/*
    Business rules definitions
*/
/**
 * @name YEARLY_HOLIDAY_HOURS
 * @global int holiday hours per year for an 8-hour working journey
 */
define ('YEARLY_HOLIDAY_HOURS', 184);

/**
 * @name ALL_USERS_GROUP
 * @global string users group used for retrieving all users
 */
define('ALL_USERS_GROUP', 'staff');

/**
 * @name USER_GROUPS
 * @global string all user groups for displaying on the interfaces as
 * a serialized array, compulsory when using LDAP user authentication
 */
define('USER_GROUPS', serialize(array('staff', 'admin', 'manager')));

/* New from PhpReport 2.18 */

/**
 * @name EXTRA_HOURS_WARNING_TRIGGER
 * @global int Value that acts as a warning trigger for extra hours values.
 */
define('EXTRA_HOURS_WARNING_TRIGGER', 50);

/**
 * @name NO_FILL_EMAIL_FROM
 * @global string Email appearing on the from header fo all the NO_FILL emails
 */
define('NO_FILL_EMAIL_FROM', 'project-management@domain.com');

/**
 * @name NO_FILL_CC_WARNING
 * @global string Comma separated list of mailboxes that will receive warning emails.
 */
define('NO_FILL_CC_WARNING', 'manager1@domain.com');

/**
 * @name NO_FILL_CC_CRITICAL
 * @global string Comma separated list of mailboxes that will receive critical emails.
 */
define('NO_FILL_CC_CRITICAL', 'manager1@domain.com, project-management@domain.com');

/**
 * @name NO_FILL_CC_LAST
 * @global string Comma separated list of mailboxes that will receive last emails.
 */
define('NO_FILL_CC_LAST', 'manager1@domain.com, project-management@domain.com');

/**
 * @name NO_FILL_TEMPLATE_WARNING
 * @global string File containing warning email.
 */
define('NO_FILL_TEMPLATE_WARNING', 'templates/no_fill_warning.txt');

/**
 * @name NO_FILL_TEMPLATE_CRITICAL
 * @global string File containing critical email.
 */
define('NO_FILL_TEMPLATE_CRITICAL', 'templates/no_fill_critical.txt');

/**
 * @name NO_FILL_TEMPLATE_LAST
 * @global string File containing last email.
 */
define('NO_FILL_TEMPLATE_LAST', 'templates/no_fill_last.txt');

/**
 * @name NO_FILL_TEMPLATE_MANAGERS
 * @global string File containing report email to the managers.
 */
define('NO_FILL_TEMPLATE_MANAGERS', 'templates/no_fill_summary_managers.txt');

/**
 * @name NO_FILL_SUBJECT_WARNING
 * @global string Subject containing warning email subject.
 */
define('NO_FILL_SUBJECT_WARNING', 'Please log your hours in PHPReport');

/**
 * @name NO_FILL_SUBJECT_CRITICAL
 * @global string Subject containing critical email subject.
 */
define('NO_FILL_SUBJECT_CRITICAL', '[URGENT] Log your hours in PHPReport');

/**
 * @name NO_FILL_SUBJECT_LAST
 * @global string Subject containing last email subject.
 */
define('NO_FILL_SUBJECT_LAST', '[URGENT] Three days remain to update PHPReport');

/**
 * @name NO_FILL_SUBJECT_MANAGERS
 * @global string Subject containing report to managers email subject.
 */
define('NO_FILL_SUBJECT_MANAGERS', 'Report: Summary of people late filling in PHPReport');

 /**
 * @name NO_FILL_DAYS_TRIGGER_WARNING
 * @global int Value in days to consider sending a warning message.
 */
define('NO_FILL_DAYS_TRIGGER_WARNING', 15);

 /**
 * @name NO_FILL_DAYS_TRIGGER_CRITICAL
 * @global int Value in days to consider sending a critical message.
 */
define('NO_FILL_DAYS_TRIGGER_CRITICAL', 21);

 /**
 * @name NO_FILL_DAYS_TRIGGER_LAST
 * @global int Value in days to consider sending the last message.
 */
define('NO_FILL_DAYS_TRIGGER_LAST', 27);

/* New from PhpReport 2.19 */

/**
 * Enable usage of an external service for authentication.
 * WARNING: this will bypass internal password check! Make sure your external
 * authentication service is correctly configured before enabling this flag.
 */
define('USE_EXTERNAL_AUTHENTICATION', false);

/**
 * HTTP header that will be provided by the external authentication service, in
 * case it is different from the default PHP_AUTH_USER. To be used in
 * combination with USE_EXTERNAL_AUTHENTICATION.
 */
define('EXTERNAL_AUTHENTICATION_USER_HEADER', '');

/**
 * Additional PostgreSQL connection parameters to be passed to pg_connect.
 */
define('EXTRA_DB_CONNECTION_PARAMETERS', '');

/* New from PhpReport 2.21 */

/**
 * Add links to external sites, like issue trackers, from the application menu.
 * URLs in this list will be added as links to the PhpReport menu bar, at the
 * top-right. They will appear from right to left. The corresponding entries in
 * ISSUE_TRACKER_LINKS_TEXT will be used as text for the links.
 */
define('ISSUE_TRACKER_LINKS_URL',
    serialize(array('https://github.com/Igalia/phpreport/issues')));

/**
 * Add links to external sites from the application menu.
 * To be used in combination with ISSUE_TRACKER_LINKS_URL, see its entry above.
 */
define('ISSUE_TRACKER_LINKS_TEXT', serialize(array('Report an issue')));

/* New from PhpReport 2.22 */

/* CalDav integration */
define('CALENDAR_URL', '');
define('CALENDAR_ID', '');
define('CALENDAR_USERNAME', '');
define('CALENDAR_PASSWORD', '');
define('CALENDAR_EMAIL', '');

/**
 * Id of the project used for identifying vacations tasks.
 * Replaces VACATIONS_PROJECT.
 * @global int vacations project id.
 */
define('VACATIONS_PROJECT_ID', 1);

/**
 * Domain for calendar appointments and emails sent to users by the vacation
 * management and fill-up reminder features. Mail addresses will be composed as
 * user login + @ + COMPANY_DOMAIN.
 * Replaces NO_FILL_EMAIL_DOMAIN.
 * @global string company domain to compose email addresses.
 */
define('COMPANY_DOMAIN', "domain.com");

/**
 * @name EMPLOYEES_GROUP
 * @global string users group used for retrieving all active employees
 */
define('EMPLOYEES_GROUP', 'staff');
