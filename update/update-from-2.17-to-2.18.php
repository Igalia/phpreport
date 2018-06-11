<?php
/*
 * Copyright (C) 2018 Igalia, S.L. <info@igalia.com>
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

define('PHPREPORT_ROOT', __DIR__ . '/../');
define('SQLPATH', PHPREPORT_ROOT . 'sql/update/');

/* These are the sql files that must be executed to prepare DB.
 *
 * IMPORTANT: they must be ordered for their proper execution.
 */
$sqlFiles = array();
$sqlFiles[] = SQLPATH . "force-non-empty-project.sql";
$sqlFiles[] = SQLPATH . "add-date-block-to-config.sql";
$sqlFiles[] = SQLPATH . "bump-db-version-2-18.sql";

// run upgrade scripts

require_once(PHPREPORT_ROOT . 'config/config.php');

if (strcmp(get_db_version(DB_HOST,DB_PORT,DB_NAME,DB_USER,DB_PASSWORD), "2.17") != 0) {
    print ("Wrong database version. " .
            "Make sure DB is on 2.17 version before running this upgrade.\n");
    exit();
}

$success = true;
foreach ($sqlFiles as $file) {
    if (!parse_psql_dump($file,DB_HOST,DB_PORT,DB_NAME,DB_USER,DB_PASSWORD)) {
        $success = false;
        break;
    }
}

// finish, print message

if ($success) {
    print ("Database update completed successfully\n");
}
else {
    print ("Error updating database in step: " . $file .
        "\nPlease consider doing a manual update\n");
}
