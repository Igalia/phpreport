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

function get_db_version($nowhost,$nowport,$nowdatabase,$nowuser,$nowpass){
    $link = pg_connect("host=$nowhost port=$nowport user=$nowuser dbname=$nowdatabase password=$nowpass");
    if (!$link) {
        return false;
    }

    $result = pg_query($link, "select count(*) from pg_class where relname='config' and relkind='r'");

    if ($result != NULL) {
        $count = pg_fetch_array($result);
        if (strcmp($count["count"], "0") == 0) {
            // version 2.0 did not have a config table
            return "2.0";
        }
    }

    $result = pg_query($link, "select version from config");

    if ($result != NULL) {
        $version = pg_fetch_array($result);
        return $version["version"];
    }

    return false;
}
