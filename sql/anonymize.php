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

/**
 * Run this script on a copy of your PhpReport database to remove any sensitive
 * data from it. It is interesting for developers to make tests with real data.
 *
 * Setup access to some PhpReport database in the settings below.
 *
 * WARNING: make sure you don't run it on your production database!
 */

/*
 * Script configuration: set these values to match your environment.
 */
define('DB_NAME', 'phpreport_copy');
define('DB_USER', 'phpreport');
define('DB_PASSWORD', 'phpreport');
define('DB_HOST', 'localhost');
define('DB_PORT', 5432);


define('PHPREPORT_ROOT', __DIR__ . '/../');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');

$loren_ipsum = DBPostgres::escapeString(<<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ac magna non augue porttitor scelerisque ac id diam. Mauris elit velit, lobortis sed interdum at, vestibulum vitae libero. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque iaculis ligula ut ipsum mattis viverra. Nulla a libero metus. Integer gravida tempor metus eget condimentum. Integer eget iaculis tortor. Nunc sed ligula sed augue rutrum ultrices eget nec odio. Morbi rhoncus, sem laoreet tempus pulvinar, leo diam varius nisi, sed accumsan ligula urna sed felis. Mauris molestie augue sed nunc adipiscing et pharetra ligula suscipit. In euismod lectus ac sapien fringilla ut eleifend lacus venenatis.

Nullam eros mi, mollis in sollicitudin non, tincidunt sed enim. Sed et felis metus, rhoncus ornare nibh. Ut at magna leo. Suspendisse egestas est ac dolor imperdiet pretium. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam porttitor, erat sit amet venenatis luctus, augue libero ultrices quam, ut congue nisi risus eu purus. Cras semper consectetur elementum. Nulla vel aliquet libero. Vestibulum eget felis nec purus commodo convallis. Aliquam erat volutpat.
EOD
);

$link = pg_connect("host=".DB_HOST.
        " port=".DB_PORT.
        " user=".DB_USER.
        " dbname=".DB_NAME.
        " password=".DB_PASSWORD);
if (!$link) error_log("ERROR: Could not connect to DB");

// Anonymize projects:
// * reset name
// * reset invoice to 10000
// * 'Holidays' project remains unchanged

$result = pg_query($link, $query="SELECT id, description FROM project");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    if ($row['description'] != 'Holidays') {
        $update_query = "UPDATE project SET description='Project {$row['id']}', ".
                "invoice=10000 ".
                "WHERE id={$row['id']}";
        print($update_query."\n");
        if (!pg_query($link, $update_query))
            error_log("ERROR: query failed: $update_query");
    }
}
pg_freeresult($result);

// Anonymize customers:
// * reset name
// * reset url

$result=pg_query($link, $query="SELECT id FROM customer");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    $update_query = "UPDATE customer SET name='Customer {$row['id']}', ".
            "url='http://www.example.com' ".
            "WHERE id={$row['id']}";
    print($update_query."\n");
    if (!pg_query($link, $update_query))
        error_log("ERROR: query failed: $update_query");
}
pg_freeresult($result);

// Anonymize sectors:
// * reset name

$result=pg_query($link, $query="SELECT id FROM sector");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    $update_query = "UPDATE sector SET name='Sector {$row['id']}' ".
            "WHERE id={$row['id']}";
    print($update_query."\n");
    if (!pg_query($link, $update_query))
        error_log("ERROR: query failed: $update_query");
}
pg_freeresult($result);

// Anonymize tasks:
// * reset task text

$update_query = "UPDATE task SET text='$loren_ipsum'";
print($update_query."\n");
if (!pg_query($link, $update_query))
    error_log("ERROR: query failed: $update_query");

// Anonymize tasks:
// * reset story

$result=pg_query($link, $query="SELECT distinct(story) FROM task");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

$count = 0;
while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    if ($row['story']) {
        $update_query = "UPDATE task SET story='Story{$count}' ".
                "WHERE story='".DBPostgres::escapeString($row['story'])."'";
        print($update_query."\n");
        if (!pg_query($link, $update_query))
            error_log("ERROR: query failed: $update_query");
        $count++;
    }
}
pg_freeresult($result);

// Anonymize extra_hours:
// * reset comment

$update_query = "UPDATE extra_hour SET comment=''";
print($update_query."\n");
if (!pg_query($link, $update_query))
    error_log("ERROR: query failed: $update_query");

// Anonymize areas:
// * reset name

$result=pg_query($link, $query="SELECT id FROM area");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    $update_query = "UPDATE area SET name='Area {$row['id']}' ".
            "WHERE id={$row['id']}";
    print($update_query."\n");
    if (!pg_query($link, $update_query))
        error_log("ERROR: query failed: $update_query");
}
pg_freeresult($result);

// Anonymize users:
// * reset login
// * clear password

$result=pg_query($link, $query="SELECT id FROM usr");
if ($result == NULL) error_log("ERROR: Could not run query: $query");

while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
    $update_query = "UPDATE usr SET login='user{$row['id']}', ".
            "password='' ".
            "WHERE id={$row['id']}";
    print($update_query."\n");
    if (!pg_query($link, $update_query))
        error_log("ERROR: query failed: $update_query");
}
pg_freeresult($result);

// Anonymize users:
// * add admin user with password 'admin'
// * register admin user in every group to have full permissions

$insert_query = "INSERT INTO usr(password, login) VALUES (md5('admin'), 'admin')";
print($insert_query."\n");
if (pg_query($link, $insert_query)) {
    $admin_id = DBPostgres::getId($link, "usr_id_seq");

    $result = pg_query($link, $query="SELECT id FROM user_group");
    if ($result == NULL) error_log("ERROR: Could not run query: $query");

    while ($row=pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
        $insert_query = "INSERT INTO belongs(usrid, user_groupid) VALUES ({$admin_id}, {$row['id']})";
        print($insert_query."\n");
        if (!pg_query($link, $insert_query))
            error_log("ERROR: query failed: $insert_query");
    }
    pg_freeresult($result);
}
else error_log("ERROR: query failed: $insert_query");
