<?php
/*
 * Copyright (C) 2013 Igalia, S.L. <info@igalia.com>
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


define('PHPREPORT_ROOT', __DIR__ . '/../');
define('SQLPATH', PHPREPORT_ROOT . 'sql/update/');

/* These are the sql files that must be executed to prepare DB.
 *
 * IMPORTANT: they must be ordered for their proper execution.
 */
$sqlFiles = array();
$sqlFiles[] = SQLPATH . "create-config-table.sql";
$sqlFiles[] = SQLPATH . "add-block-task-columns-in-config-table.sql";
$sqlFiles[] = SQLPATH . "add-onsite-column-to-task.sql";
$sqlFiles[] = SQLPATH . "remove-triggers-for-overlapping-control.sql";

// function inspired by code from install/setup-config.php
function parse_psql_dump($url,$nowhost,$nowport,$nowdatabase,$nowuser,$nowpass){
    $link = pg_connect("host=$nowhost port=$nowport user=$nowuser dbname=$nowdatabase password=$nowpass");
    if (!$link) {
        return false;
    }

    $file_content = file($url);
    $string = "";
    $success = true;
    foreach($file_content as $sql_line){
        $string = $string . $sql_line;
        if(trim($string) != "" && strstr($string, "--") === false){
            if (strstr($string, "\\.") != false)
            {
                pg_put_line($link, $string);
                pg_end_copy($link);
                $string = "";
            } elseif (strstr($string, ";") != false)
            {
                if (!pg_query($link, $string)) {
                    $success = false;
                }
                $string = "";
            }
        } else $string = "";
    }

    return $success;
}

// run upgrade scripts
$success = true;
require_once(PHPREPORT_ROOT . 'config/config.php');
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
