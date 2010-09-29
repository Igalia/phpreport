<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
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
 * @author Jacobo Aragunde P�rez <jaragunde@igalia.com>
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
define('DB_HOST', 'localhost');

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
define('LDAP_PORT', 5389);

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
  Configuration for NavalPlan integration
*/
define('CREATE_REPORT_POSTACTION', 'SendTaskToNavalPlanPlugin');
define('UPDATE_REPORT_POSTACTION', 'SendTaskToNavalPlanPlugin');
define('PARTIAL_UPDATE_REPORT_POSTACTION', 'SendTaskToNavalPlanPlugin');
define('NAVALPLAN_SERVICE_URL', 'http://localhost:8080/navalplanner-webapp/ws/rest');
define('NAVALPLAN_USER', 'wswriter');
define('NAVALPLAN_PASSWORD', 'wswriter');


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
define('USER_GROUPS', serialize(array('staff')));

/**
 * @name VACATIONS_PROJECT
 * @global string project used for identifying vacations tasks
 */
define('VACATIONS_PROJECT', 'vac');
