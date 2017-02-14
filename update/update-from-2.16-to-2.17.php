<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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

require_once('utils.php');

/**
 * This script selects the customer id related to each project and populate the new customerid
 * column in project table.
 */

define('PHPREPORT_ROOT', __DIR__ . '/../');
define('SQLPATH', PHPREPORT_ROOT . 'sql/update/');

/* These are the sql files that must be executed to prepare DB.
 *
 * IMPORTANT: they must be ordered for their proper execution.
 */
$sqlFiles = array();
$sqlFiles[] = SQLPATH . "add-manager-user-group.sql";
$sqlFiles[] = SQLPATH . "create-template-table.sql";
$sqlFiles[] = SQLPATH . "create-user_goals-table.sql";
$sqlFiles[] = SQLPATH . "update-project-add-customer-relation.sql";
$sqlFiles[] = SQLPATH . "bump-db-version-2-17.sql";

// run upgrade scripts

require_once(PHPREPORT_ROOT . 'config/config.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');


if (strcmp(get_db_version(DB_HOST,DB_PORT,DB_NAME,DB_USER,DB_PASSWORD), "2.16") != 0) {
    print ("Wrong database version. " .
        "Make sure DB is on 2.16 version before running this upgrade.\n");
    exit();
}

$success = true;
foreach ($sqlFiles as $file) {
    if (!parse_psql_dump($file,DB_HOST,DB_PORT,DB_NAME,DB_USER,DB_PASSWORD)) {
        $success = false;
        break;
    }
}

if ($success) {
    $link = pg_connect("host=".DB_HOST.
        " port=".DB_PORT.
        " user=".DB_USER.
        " dbname=".DB_NAME.
        " password=".DB_PASSWORD);

    if (!$link) {
        error_log("ERROR: Could not connect to DB");
    }

    $result = pg_query($link, $query = "SELECT customerid, projectid FROM requests");
    if ($result == null) {
        error_log("ERROR: Could not run query: $query");
    }

    $customerAssignedToProject = array();

    while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $customerAssignedToProject[$row['projectid']][] = $row['customerid'];
    }

    foreach ($customerAssignedToProject as $projectId => $customerList) {
        $customerAssociated = $customerList[0];
        if (count($customerList) > 1) {
            $taskCustomerFreq = array();
            print("Project with id: $projectId is assigned to customers with id: ");
            $str = "";
            foreach ($customerList as $customerId) {
                $str .= $customerId . ",";
                // We have more than one customers for these projects, so lets check
                // in tasks table
                $result = pg_query($link, $query = "select customerid, count(*) as freq from task where projectid=$projectId group by customerid;");
                $rows = pg_fetch_all($result);

                foreach ($rows as $row) {
                    // There are chances that null shows up, lets skip it
                    if ($row['customerid'] != null) {
                        $taskCustomerFreq[$row['customerid']] = $row['freq'];
                    }
                }
            }
            // Finish the debug script
            print(rtrim($str, ",") . "\n");
            //We sort by value to get the highest number of customer association
            arsort($taskCustomerFreq);
            if (count($taskCustomerFreq) > 0) {
                $customerAssociated = key($taskCustomerFreq);
            }
        }

        $update_query = "UPDATE project SET customerid= $customerAssociated where id= $projectId";
        print("Project with id: $projectId will be assigned to customer with id: $customerAssociated" . "\n");
        if (!pg_query($link, $update_query)) {
            error_log("ERROR: query failed: $update_query");
        }
    }

    pg_freeresult($result);

    print ("Database update completed successfully\n");
} else {
    print ("Error updating database. Please consider doing a manual update\n");
}

