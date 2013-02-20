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


define('PHPREPORT_ROOT', __DIR__ . '/../');
include_once(PHPREPORT_ROOT . '/model/facade/action/ExtraHoursReportAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');

//declare readline() function if the system function is not available
if (!function_exists("readline")) {

    function readline($prompt) {
        echo $prompt;
        return substr(fgets(STDIN),0, -1);
    }
}

// Special sector name (used in case we find a non-matching sector id)
$unidentified_sector = "others";

// Special area name (used in case we find a non-matching area id)
$unidentified_area = "Unidentified";

if ($argc<11)
    die("\n  Usage: \n\t$argv[0] source-host source-port source-db source-db-user \n\t\tsource-db-password destination-host destination-port \n\t\tdestination-db destination-db-user destination-db-password\n\n");


$die1="The source database is not operative at this moment. Please, try the operation later.\n";
$die2="The destination database is not operative at this moment. Please, try the operation later.\n";
$cnx1 = pg_connect("host=$argv[1] port=$argv[2] user=$argv[4] dbname=$argv[3] password=$argv[5]")
 or die($die1);

$cnx2 = pg_connect("host=$argv[6] port=$argv[7] user=$argv[9] dbname=$argv[8] password=$argv[10]")
 or die($die2);


// --------------------------------- GROUPS CREATION


if (!$result=@pg_query($cnx2,"INSERT INTO user_group(name) VALUES ('admin'), ('staff')")) {
    $error="It has not been possible to insert the new groups from values ('admin'), ('staff').\n";
    print ($error);
   }



// --------------------------------- USERS MIGRATION


// We look for both user groups so we get their ids

$result=@pg_query($cnx2,$query="SELECT * FROM user_group where name ='admin'")
or die($die2);

$row=@pg_fetch_array($result);

$admin = $row["id"];



$result=@pg_query($cnx2,$query="SELECT * FROM user_group where name ='staff'")
or die($die2);

$row=@pg_fetch_array($result);

$staff = $row["id"];


// We go over users table and insert their data in the new table

$result=@pg_query($cnx1,$query="SELECT * FROM users")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $password = "NULL";

 if ($row["password"] != NULL)
    $password = "'{$row["password"]}'";

  if (!$result2=@pg_query($cnx2,"INSERT INTO usr(password, login) VALUES ($password, " . DBPostgres::checkStringNull($row["uid"]) . ")")) {
    $error=("It has not been possible to insert the new user from values ($password, '{$row["uid"]}').\n");
    print ($error);
   }


  // We look for the stored user in order to have its identifier

  $result2=@pg_query($cnx2,$query="SELECT * FROM usr where login =" . DBPostgres::checkStringNull($row["uid"]))
  or die($die2);

  $row2=@pg_fetch_array($result2);


  // We insert new rows in the table 'belongs' if the flags tell us this user belongs to a group

  if ($row["admin"] == 't')
    if (!$result2=@pg_query($cnx2,"INSERT INTO belongs(user_groupid, usrid) VALUES ('$admin', '{$row2["id"]}')")) {
      $error=("It has not been possible to match the new user with admin from values ('$admin', '{$row2["id"]}').\n");
      die ($error);
    }

 if ($row["staff"] == 't')
    if (!$result2=@pg_query($cnx2,"INSERT INTO belongs(user_groupid, usrid) VALUES ('$staff', '{$row2["id"]}')")) {
      $error=("It has not been possible to match the new user with staff from values ('$staff', '{$row2["id"]}').\n");
      print ($error);
    }

}




// --------------------------------- EXTRA HOURS MIGRATION


// We just get the data from the old table and put it in the new one, looking for the new user identifier

$result=@pg_query($cnx1,$query="SELECT * FROM extra_hours")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

  $result2=@pg_query($cnx2,$query="SELECT * FROM usr where login =" . DBPostgres::checkStringNull($row["uid"]))
  or die($die2);

  $row2=@pg_fetch_array($result2);
  if (!$result2=@pg_query($cnx2,"INSERT INTO extra_hour(_date, hours, usrid) VALUES ('{$row["date"]}', " . DBPostgres::checkNull($row["hours"]) . ", " . DBPostgres::checkNull($row2["id"]) . ")")) {
    $error=("It has not been possible to insert the new extra hour for user '{$row["uid"]}' from values ('{$row["date"]}', '{$row["hours"]}', '{$row2["id"]}').\n");
    print ($error);
   }

}




// --------------------------------- CUSTOMERS SECTORS MIGRATION


// We just get the data from the old table and put it in the new one

$result2=@pg_query($cnx2,"ALTER TABLE sector ADD COLUMN id_ant varchar(256)")
or die($die2);

$result=@pg_query($cnx1,$query="SELECT * FROM label WHERE type = 'csector'")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 if (!$result2=@pg_query($cnx2,"INSERT INTO sector(name, id_ant) VALUES ( " . DBPostgres::checkStringNull($row["description"]) . ", " . DBPostgres::checkStringNull($row["code"]) . ")")) {
    $error=("It has not been possible to insert the new sector from values ('{$row["description"]}', '{$row["code"]}').\n");
    print ($error);
   }

}

//  Uncomment this block if you don't have a special sector for other sectors

/*if (!$result2=@pg_query($cnx2,"INSERT INTO sector(name, id_ant) VALUES ('$unidentified_sector', '$unidentified_sector')")) {
    $error=("It has not been possible to insert the new sector from values ('$unidentified_sector', '$unidentified_sector').\n");
    print ($error);
 }*/




// --------------------------------- AREAS MIGRATION


// We just get the data from the old table and put it in the new one

$result2=@pg_query($cnx2,"ALTER TABLE area ADD COLUMN id_ant varchar(256)")
or die($die2);

$result=@pg_query($cnx1,$query="SELECT * FROM label WHERE type = 'parea'")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 if (!$result2=@pg_query($cnx2,"INSERT INTO area(name, id_ant) VALUES (" . DBPostgres::checkStringNull($row["description"]) . ", " . DBPostgres::checkStringNull($row["code"]) . ")")) {
    $error=("It has not been possible to insert the new area from values ('{$row["description"]}', '{$row["code"]}').\n");
    print ($error);
   }

}

if (!$result2=@pg_query($cnx2,"INSERT INTO area(name, id_ant) VALUES ('Not assigned', 'Not assigned')")) {
    $error=("It has not been possible to insert the new area from values ('Not assigned', 'Not assigned').\n");
    print ($error);
 }

if (!$result2=@pg_query($cnx2,"INSERT INTO area(name, id_ant) VALUES ('$unidentified_area', '$unidentified_area')")) {
    $error=("It has not been possible to insert the new area from values ('$unidentified_area', '$unidentified_area').\n");
    print ($error);
 }




// --------------------------------- CUSTOMERS MIGRATION


// We just get the data from the old table and put it in the new one, creating a temporary column in order to store their old identifier

$result=@pg_query($cnx1,$query="SELECT * FROM customer")
or die($die1);

$result2=@pg_query($cnx2,"ALTER TABLE customer ADD COLUMN id_ant varchar(256)")
or die($die2);// We just get the data from the old table and put it in the new one, looking for the new user identifier

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $url = "{$row["url"]}";

 $result2=@pg_query($cnx2,$query="SELECT * FROM sector WHERE id_ant =" . DBPostgres::checkStringNull($row["sector"]))
    or die($die2);

 $row2=@pg_fetch_array($result2);

 if ($row2["id"] == NULL)
 {

    print ("Sector with old id '{$row["sector"]}' does not exist. Customer '{$row["name"]}' will be assigned to sector '$unidentified_sector'.\n");

    $result2=@pg_query($cnx2,$query="SELECT * FROM sector WHERE id_ant ='$unidentified_sector'")
    or die($die2);

    $row2=@pg_fetch_array($result2);

 }

 if (!$result2=@pg_query($cnx2,"INSERT INTO customer(name, type, sectorid, url, id_ant) VALUES (" . DBPostgres::checkStringNull($row["name"]) . ", " . DBPostgres::checkStringNull($row["type"]) . ", '{$row2["id"]}', " . DBPostgres::checkStringNull($url) . ", '{$row["id"]}')")) {
    $error=("It has not been possible to insert the new customer from values ('{$row["name"]}', '{$row["type"]}', '{$row2["id"]}',  $url, '{$row["id"]}').\n");
    print ($error);
   }

}




// --------------------------------- PROJECTS AND THEIR RELATIONSHIP WITH CUSTOMERS MIGRATION


// We just get the data from the old table and put it in the new one, creating a temporary column in order to store their old identifier

$result=@pg_query($cnx1,$query="SELECT * FROM projects")
or die($die1);

$result2=@pg_query($cnx2,"ALTER TABLE project ADD COLUMN id_ant varchar(256)")
or die($die2);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $init = "NULL";
 $_end = "NULL";

 if ($row["init"] != NULL)
    $init = "'{$row["init"]}'";

 if ($row["_end"] != NULL)
    $_end = "'{$row["_end"]}'";

 $invoice = $row["invoice"];

 $est_hours = $row["est_hours"];

 if ($row["area"] == NULL)
 {

  $result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant ='Not assigned'")
    or die($die2);

  $row2=@pg_fetch_array($result2);

  $area = "'{$row2["id"]}'";

 }
 else
 {

  $result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant =" . DBPostgres::checkStringNull($row["area"]))
    or die($die2);

  $row2=@pg_fetch_array($result2);

  if ($row2["id"] == NULL)
  {

    print ("Area with old id '{$row["area"]}' does not exist. Project '{$row["id_ant"]}' will be assigned to area '$unidentified_area'.\n");

    $result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = '$unidentified_area'")
    or die($die2);

    $row2=@pg_fetch_array($result2);

  }

  $area = "'{$row2["id"]}'";

 }

 $description = $row["description"];

 $type = $row["type"];

 $moved_hours = $row["moved_hours"];

 $sched_type = $row["sched_type"];

 if (strtolower($row['activation']) == "t")
    $activation = True;
 else $activation = False;

 if (!$result2=@pg_query($cnx2,"INSERT INTO project(activation, init, _end, invoice, est_hours, areaid, description, type, moved_hours, sched_type, id_ant) VALUES (" . DBPostgres::boolToString($activation) . ", $init, $_end, " . DBPostgres::checkNull($invoice) . ", " . DBPostgres::checkNull($est_hours) . ", $area, " . DBPostgres::checkStringNull($description) . ", " . DBPostgres::checkStringNull($type) . ", " . DBPostgres::checkNull($moved_hours) . ", " . DBPostgres::checkStringNull($sched_type) . ", '{$row["id"]}')")) {
    $error=("It has not been possible to insert the new project from values ('{$activation}', $init, $_end,  $invoice, $est_hours, $area, $description, $type, $moved_hours, $sched_type, '{$row["id"]}').\n");
    print ($error);
   }

// If it has a client, we add it in the middle table 'requests', so we need the new user and project identifiers

 if ($row["customer"] != NULL)
 {
    $result2=@pg_query($cnx2,$query="SELECT * FROM customer WHERE id_ant ='{$row["customer"]}'")
    or die($die2);

    $row2=@pg_fetch_array($result2);

    $result2=@pg_query($cnx2,$query="SELECT * FROM project WHERE id_ant ='{$row["id"]}'")
    or die($die2);

    $row3=@pg_fetch_array($result2);

    if (!$result2=@pg_query($cnx2,"INSERT INTO requests(customerid, projectid) VALUES ('{$row2["id"]}', '{$row3["id"]}')")) {
            $error=("It has not been possible to insert the new relation requests from values ('{$row2["id"]}', '{$row3["id"]}').\n");
            print ($error);
    }

 }



}




// --------------------------------- RELATIONSHIP BETWEEN USERS AND PROJECTS MIGRATION


// We just get the data from the old table and put it in the new one, looking for the new user and project identifiers

$result=@pg_query($cnx1,$query="SELECT * FROM project_user")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $result2=@pg_query($cnx2,$query="SELECT * FROM usr where login ='{$row["uid"]}'")
  or die($die2);

  $row2=@pg_fetch_array($result2);

 $result2=@pg_query($cnx2,$query="SELECT * FROM project where id_ant ='{$row["name"]}'")
  or die($die2);

  $row3=@pg_fetch_array($result2);

 if (!$result2=@pg_query($cnx2,"INSERT INTO project_usr(usrid, projectid) VALUES ('{$row2["id"]}', '{$row3["id"]}')")) {
    $error=("It has not been possible to insert the new working relation from values ('{$row2["id"]}', '{$row3["id"]}').\n");
    print ($error);
   }

}



// --------------------------------- LABELS MIGRATION

$result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = '$unidentified_area'")
    or die($die2);

$row2=@pg_fetch_array($result2);

$unidentifiedArea = $row2["id"];

$result2=@pg_query($cnx2,$query="SELECT * FROM sector WHERE id_ant ='$unidentified_sector'")
    or die($die2);

$row2=@pg_fetch_array($result2);

$unidentifiedSector = $row2["id"];

$typeProjects = array ();
$typeCustomers = array();

$result=@pg_query($cnx1,$query="SELECT DISTINCT (type) FROM task")
    or die($die2);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

    $result2=@pg_query($cnx1,$query="SELECT * FROM label WHERE code = '{$row["type"]}'")
    or die($die2);

    $row2=@pg_fetch_array($result2);

    if (strtolower($row2['activation']) == "t")
    {

          switch ($row["type"])
          {

            case "pex":
                break;

            case "pin":

                print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
                $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newCustomer)
                    $newCustomer = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result4=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                    {
                            $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', '$newCustomer', 'internal').\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeCustomers[$row["type"]] = $row3["id"];

                break;

            default:

                print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
                $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newProject)
                    $newProject = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result4=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newProject) . ")"))
                    {
                            $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ").\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeProjects[$row["type"]] = $row3["id"];



                print("\n\n---> Customer for project '$newProject' is not assigned.");
                $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '$newProject'): ");
                if (!$newCustomer)
                    $newCustomer = $newProject;

                $result4=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row4=@pg_fetch_array($result4,NULL,PGSQL_ASSOC);

                if (is_null($row4["id"]))
                {
                    if (!$result4=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                    {
                            $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal').\n");
                            print ($error);
                        break;
                    }

                    $result4=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                    or die($die2);

                    $row4=@pg_fetch_array($result4,NULL,PGSQL_ASSOC);
                }

                if (!$result2=@pg_query($cnx2,"INSERT INTO requests(customerid, projectid) VALUES ('{$row4["id"]}', '{$row3["id"]}')")) {
                        $error=("It has not been possible to insert the new relation requests from values ('{$row4["id"]}', '{$row3["id"]}').\n");
                        print ($error);
                }

                break;

          }

    }

}






// --------------------------------- TASKS MIGRATION


// We just get the data from the old table and put it in the new one, looking for the new user identifier

$result=@pg_query($cnx1,$query="SELECT * FROM task")
or die($die1);

$userIgnore = array();

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $row["uid"] = trim($row["uid"]);

 $result2=@pg_query($cnx2,$query="SELECT * FROM usr WHERE login =" . DBPostgres::checkStringNull($row["uid"]))
  or die($die2);

  $row2=@pg_fetch_array($result2);

   if (isset($userIgnore[$row["uid"]]))
   {
      if ($userIgnore[$row["uid"]] != TRUE)
          $ignore = false;
      else $ignore = true;
   } else $ignore = false;

   if (is_null($row2["id"]) && (!$ignore))
   {
    $newUser = readline("\n\n---> User with 'uid' = '{$row["uid"]}' does not exist in DB. Do you want to create him/her? (otherwise, his/her tasks will be ignored)  (Y/N): ");
    while (($newUser != "Y") && ($newUser != "y") && ($newUser != "N") && ($newUser != "n"))
        $newUser = readline("\n---> Please type a valid value (Y/N):");
    print "\n";

    if (($newUser == "Y") || ($newUser == "y"))
    {   if (!$result2=@pg_query($cnx2,"INSERT INTO usr(login) VALUES (" . DBPostgres::checkStringNull($row["uid"]) . ")")){
                $error=("It has not been possible to insert the new user from values ('{$row["uid"]}').\n");
                print ($error);
        }
        $result2=@pg_query($cnx2,$query="SELECT * FROM usr WHERE login =" . DBPostgres::checkStringNull($row["uid"]))
        or die($die2);

        $row2=@pg_fetch_array($result2);

    }
    else
        $userIgnore[$row["uid"]] = TRUE;

   }

 if (isset($userIgnore[$row["uid"]]))
 {
    if ($userIgnore[$row["uid"]] != TRUE)
        $ignore = false;
    else $ignore = true;
 } else $ignore = false;

  if (!$ignore)
  {

  $ttype = $row["ttype"];
  $phase = $row["phase"];
  $text = $row["text"];
  $story = $row["story"];

  if (strtolower($row["telework"]) == 't')
    $telework = true;
  else $telework = false;


  if ($row["name"] == NULL)
    $projectid = "NULL";
  else
  {
    $result2=@pg_query($cnx2,$query="SELECT * FROM project WHERE id_ant =" . DBPostgres::checkStringNull($row["name"]))
    or die($die2);

    $row3=@pg_fetch_array($result2);

    $projectid = "'{$row3["id"]}'";
  }


  if ($row["customer"] == NULL)
    $customerid = "NULL";
  else
  {

    $result2=@pg_query($cnx2,$query="SELECT * FROM customer WHERE id_ant =" . DBPostgres::checkStringNull($row["customer"]))
    or die($die2);

    $row4=@pg_fetch_array($result2);

    $customerid = "'{$row4["id"]}'";

  }


  switch ($row["type"])
  {

    case "pex":
        break;

    case "vent":

        if (($customerid == "NULL")&&($projectid == "NULL"))
        {

            if (!isset($typeCustomers[$row["type"]]))
            {
                print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
                $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newCustomer)
                    $newCustomer = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result2=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                    {
                            $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal').\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeCustomers[$row["type"]] = $row3["id"];

            }

            $customerid = $typeCustomers[$row["type"]];

            if (!isset($typeProjects[$row["type"]]))
            {
                print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
                $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newProject)
                    $newProject = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newProject) . ")"))
                    {
                            $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ").\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeProjects[$row["type"]] = $row3["id"];

            }

            $projectid = $typeProjects[$row["type"]];

            break;

        }

        if ($projectid == "NULL")
        {

            if (!isset($typeProjects[$row["type"]]))
            {
                print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
                $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newProject)
                    $newProject = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newProject) . ")"))
                    {
                            $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ").\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeProjects[$row["type"]] = $row3["id"];

            }

            $projectid = $typeProjects[$row["type"]];

            !$result2=@pg_query($cnx2,"INSERT INTO requests(customerid, projectid) VALUES ($customerid, $projectid)");

            break;

        }

        if (!isset($typeCustomers[$row["type"]]))
        {
            print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
            $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newCustomer)
                $newCustomer = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                {
                        $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', '$newCustomer', 'internal').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeCustomers[$row["type"]] = $row3["id"];

        }

        $customerid = $typeCustomers[$row["type"]];

        $ttype = "'sales'";

        break;

    case "adm":

        if ($projectid == "NULL")
        {

            if (!isset($typeProjects[$row["type"]]))
            {
                print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
                $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
                if (!$newProject)
                    $newProject = $row["type"];

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newProject) . ")"))
                    {
                            $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ").\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeProjects[$row["type"]] = $row3["id"];

            }

            $projectid = $typeProjects[$row["type"]];

            break;

        }


        if (!isset($typeCustomers[$row["type"]]))
        {
            print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
            $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newCustomer)
                $newCustomer = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                {
                        $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', '$newCustomer', 'internal').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeCustomers[$row["type"]] = $row3["id"];

        }

        $customerid = $typeCustomers[$row["type"]];

        $ttype = "'adm'";

        break;


    case "pin":

        if (!isset($typeCustomers[$row["type"]]))
        {
            print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
            $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newCustomer)
                $newCustomer = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                {
                        $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', '$newCustomer', 'internal').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeCustomers[$row["type"]] = $row3["id"];

        }

        $customerid = $typeCustomers[$row["type"]];

        break;

    case "coge":
    case "asam":

        if (!isset($typeCustomers[$row["type"]]))
        {
            print("\n\n---> Customer for type '{$row["type"]}' is not assigned.");
            $newCustomer = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newCustomer)
                $newCustomer = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO customer(sectorid, name, type) VALUES ('$unidentifiedSector', " . DBPostgres::checkStringNull($newCustomer) . ", 'internal')"))
                {
                        $error=("It has not been possible to insert the new customer from values ('$unidentifiedSector', '$newCustomer', 'internal').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM customer WHERE name =" . DBPostgres::checkStringNull($newCustomer))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeCustomers[$row["type"]] = $row3["id"];

        }

        $customerid = $typeCustomers[$row["type"]];

        if (!isset($typeProjects[$row["type"]]))
        {
            print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
            $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newProject)
                $newProject = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ")"))
                {
                        $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', '$newCustomer').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeProjects[$row["type"]] = $row3["id"];

        }

        $projectid = $typeProjects[$row["type"]];

        break;

    case "vac":
    case "hsin":
    case "ap":
    case "hac":

        if (!isset($typeProjects[$row["type"]]))
        {
            print("\n\n---> Project for type '{$row["type"]}' is not assigned.");
            $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named '{$row["type"]}'): ");
            if (!$newProject)
                $newProject = $row["type"];

            $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
            or die($die2);

            $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

            if (is_null($row3["id"]))
            {
                if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newCustomer) . ")"))
                {
                        $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', '$newCustomer').\n");
                        print ($error);
                    break;
                }

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
            }
            $typeProjects[$row["type"]] = $row3["id"];

        }

        $projectid = $typeProjects[$row["type"]];

        break;

    default:

        if ($projectid != "NULL")
            break;

         if (!isset($typeProjects[$row["type"]]))
         {
            if (!isset($typeProjects["__default__"]))
            {
                print("\n\n---> Project for other types is not assigned.");
                $newProject = readline("\nType a name for it (if it doesn't exist in DB, it'll be created; if you leave it in blank, it will be named 'default'): ");
                if (!$newProject)
                    $newProject = 'default';

                $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                or die($die2);

                $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);

                if (is_null($row3["id"]))
                {
                    if (!$result2=@pg_query($cnx2,"INSERT INTO project(areaid, description) VALUES ('$unidentifiedArea', " . DBPostgres::checkStringNull($newProject) . ")"))
                    {
                            $error=("It has not been possible to insert the new project from values ('$unidentifiedArea', '$newProject').\n");
                            print ($error);
                        break;
                    }

                    $result3=@pg_query($cnx2,$query="SELECT * FROM project WHERE description =" . DBPostgres::checkStringNull($newProject))
                    or die($die2);

                    $row3=@pg_fetch_array($result3,NULL,PGSQL_ASSOC);
                }
                $typeProjects["__default__"] = $row3["id"];

            }

            $projectid = $typeProjects["__default__"];
         }
         else $projectid = $typeProjects[$row["type"]];


        break;

  }


 if (!$result2=@pg_query($cnx2,"INSERT INTO task(_date, init, _end, story, telework, text, ttype, phase, usrid, projectid, customerid) VALUES ('{$row["_date"]}', '{$row["init"]}', '{$row["_end"]}', " . DBPostgres::checkStringNull($story) . ", " . DBPostgres::boolToString($telework) . ", " . DBPostgres::checkStringNull($text) . ", " . DBPostgres::checkStringNull($ttype) . ", " . DBPostgres::checkStringNull($phase) . ", '{$row2["id"]}', $projectid, $customerid)")) {
    $error=("It has not been possible to insert the new task for user '{$row["uid"]}' from values ('{$row["_date"]}', '{$row["init"]}', '{$row["_end"]}', $story, $telework, $text, $ttype, $phase, '{$row2["id"]}', $projectid, $customerid).\n");
    print ($error);
   }
  }

}


// --------------------------------- CITY MIGRATION


// We just get the data from the old table and put it in the new one

$result=@pg_query($cnx1,$query="SELECT DISTINCT(city) FROM holiday")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 if (!$result2=@pg_query($cnx2,"INSERT INTO city(name) VALUES (" . DBPostgres::checkStringNull($row["city"]) . ")")) {
    $error=("It has not been possible to insert the new city from values ('{$row["city"]}').\n");
    print ($error);
   }

}




// --------------------------------- HOLIDAYS MIGRATION


// We just get the data from the old table and put it in the new one, looking for the new city identifier

$result=@pg_query($cnx1,$query="SELECT * FROM holiday")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $result2=@pg_query($cnx2,$query="SELECT * FROM city WHERE name =" . DBPostgres::checkStringNull($row["city"]))
    or die($die2);

 $row2=@pg_fetch_array($result2);

 if (!$result2=@pg_query($cnx2,"INSERT INTO common_event(cityid, _date) VALUES ('{$row2["id"]}', '{$row["fest"]}')")) {
    $error=("It has not been possible to insert the new common event from values ('{$row2["id"]}', '{$row["fest"]}').\n");
    print ($error);
   }

}




// --------------------------------- PERSONAL EVENTS MIGRATION


// We just get the data from the old table and put it in the new one, looking for the new user identifier

$result=@pg_query($cnx1,$query="SELECT * FROM busy_hours")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

 $result2=@pg_query($cnx2,$query="SELECT * FROM usr WHERE login =" . DBPostgres::checkStringNull($row["uid"]))
    or die($die2);

 $row2=@pg_fetch_array($result2);

 if (!$result2=@pg_query($cnx2,"INSERT INTO custom_event(_date, hours, type, usrid) VALUES ('{$row["date"]}', " . DBPostgres::checkNull($row["hours"]) . ", " . DBPostgres::checkStringNull($row["ev_type"]) . ", '{$row2["id"]}')")) {
    $error=("It has not been possible to insert the new custom event for user '{$row["uid"]}' from values ('{$row["date"]}', '{$row["hours"]}', '{$row["ev_type"]}', '{$row2["id"]}').\n");
    print ($error);
   }

}



// --------------------------------- PERIODS TO HISTORY MIGRATION (DONE FOR EACH USER)


// Here comes the real mess. We first go round the user table.

$result=@pg_query($cnx2,$query="SELECT * FROM usr")
or die($die2);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

    $result2=@pg_query($cnx1,$query="SELECT * FROM periods WHERE uid = " . DBPostgres::checkStringNull($row["login"]) . " ORDER BY init ASC")
    or die($die1);


    // We initialize the variables used to compare data.

    $city_ant = "NULL";
    $city_ant_date = "NULL";
    $hour_cost_ant = "NULL";
    $hour_cost_ant_date = "NULL";
    $journey_ant = "NULL";
    $journey_ant_date = "NULL";
    $area_ant = "NULL";
    $area_ant_date = "NULL";
    $last_date = "NULL";

    $historico = false;

    // We go over periods rows.

    while ($row2=@pg_fetch_array($result2,NULL,PGSQL_ASSOC)) {

     $historico = true;

     if ($row2["_end"]==NULL)
     {

         if (is_null($row2["city"]))
            $city_ant = "NULL";
         else $city_ant = "'{$row2["city"]}'";

         if ($row2["journey"] == NULL)
            $journey_ant = "NULL";
         else
            $journey_ant = "'{$row2["journey"]}'";

         if ($row2["area"] == NULL)
            $area_ant = "'Not assigned'";
         else
            $area_ant = "'{$row2["area"]}'";

         if ($row2["hour_cost"] == NULL)
         {
            print "A NULL value for hour cost has been detected (user '{$row["login"]}' and init date '{$row2["init"]}'). It'll be changed to '0'.\n";
            $hour_cost_ant = "'0'";
         }
         else $hour_cost_ant = "'{$row2["hour_cost"]}'";

         if ($city_ant_date == "NULL")
            $city_ant_date = "'{$row2["init"]}'";

         if ($journey_ant_date == "NULL")
            $journey_ant_date = "'{$row2["init"]}'";

         if ($hour_cost_ant_date == "NULL")
            $hour_cost_ant_date = "'{$row2["init"]}'";

         if ($area_ant_date == "NULL")
            $area_ant_date = "'{$row2["init"]}'";


     }
    else
     {
        $longTimeAgo=FALSE;

        if ($last_date != "NULL")
        {
            $aux_date1 = date_create($row2["init"]);
            $aux_date2 = date_create($last_date);
            $aux_diff = $aux_date1->diff($aux_date2);

            if (($aux_diff->days)>1)
                $longTimeAgo=TRUE;
        }

        // We compare each data with its stored value. If it's not the same, we must store a new row in its history which starts at the date
        // stored in the init variable and ends at the date stored in the end variable. If it's about a city, we must get its identifier.

        if ((($area_ant != "'{$row2["area"]}'") && !(($area_ant == "NULL") && ($row2["area"] == NULL))) || $longTimeAgo ){

            if ($area_ant != "NULL")
            {

                $result3=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = $area_ant")
                 or die($die2);

                $row3=@pg_fetch_array($result3);

                if ($row3["id"] == NULL)
                  {

                    print ("Area with old id '$area_ant' does not exist. Area history for user '{$row["login"]}' and init date '{$row2["_end"]}'  will be assigned to area '$unidentified_area'.\n");

                    $result3=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = '$unidentified_area'")
                    or die($die2);

                    $row3=@pg_fetch_array($result3);

                  }

                if (!$result3=@pg_query($cnx2,"INSERT INTO area_history(areaid, init_date, end_date, usrid) VALUES ('{$row3["id"]}', $area_ant_date, '{$row2["init"]}', '{$row["id"]}')")) {
                        $error=("It has not been possible to insert the new area period for user '{$row["login"]}' from values ('{$row3["id"]}',  $area_ant_date, '{$row2["init"]}', '{$row["id"]}').\n");
                        print ($error);
                }
            }

            if ($row2["area"] == NULL)
                $area_ant = "'Not assigned'";
            else
                $area_ant = "'{$row2["area"]}'";

            $area_ant_date = "'{$row2["init"]}'";
        } elseif ($area_ant_date == "NULL")
            $area_ant_date = "'{$row2["init"]}'";


        if ((($journey_ant != "'{$row2["journey"]}'") && !(($journey_ant == "NULL") && ($row2["journey"] == NULL))) || $longTimeAgo ){

            if ($journey_ant != "NULL")
                if (!$result3=@pg_query($cnx2,"INSERT INTO journey_history(journey, init_date, end_date, usrid) VALUES (" . DBPostgres::checkNull($journey_ant) . ", $journey_ant_date, '$last_date', '{$row["id"]}')")) {
                        $error=("It has not been possible to insert the new journey period for user '{$row["login"]}' from values ($journey_ant, $journey_ant_date, '$last_date', '{$row["id"]}').\n");
                        print ($error);
                }
            if ($row2["journey"] == NULL)
                $journey_ant = "NULL";
            else
                $journey_ant = "'{$row2["journey"]}'";
            $journey_ant_date = "'{$row2["init"]}'";
        } elseif ($journey_ant_date == "NULL")
            $journey_ant_date = "'{$row2["init"]}'";


        if ((($hour_cost_ant != "'{$row2["hour_cost"]}'") && !(($hour_cost_ant == "NULL") &&($row2["hour_cost"] == NULL))) || $longTimeAgo ){
            if ($hour_cost_ant != "NULL")
                if (!$result3=@pg_query($cnx2,"INSERT INTO hour_cost_history(hour_cost, init_date, end_date, usrid) VALUES (" . DBPostgres::checkNull($hour_cost_ant) . ", $hour_cost_ant_date, '$last_date', '{$row["id"]}')")) {
                        $error=("It has not been possible to insert the new hour cost period for user '{$row["login"]}' from values ($hour_cost_ant, $hour_cost_ant_date, '$last_date', '{$row["id"]}').\n");
                        print ($error);
                }
            if (is_null($row2["hour_cost"]))
            {
                print "A NULL value for hour cost has been detected. It'll be changed to '0'.\n";
                $hour_cost_ant = "'0'";
            }
            else $hour_cost_ant = "'{$row2["hour_cost"]}'";
            $hour_cost_ant_date = "'{$row2["init"]}'";
        } elseif ($hour_cost_ant_date == "NULL")
            $hour_cost_ant_date = "'{$row2["init"]}'";


        if ((($city_ant != "'{$row2["city"]}'") && !(($city_ant == "NULL") &&($row2["city"] == NULL))) || $longTimeAgo ){

            if ($city_ant != "NULL")
            {

                $result3=@pg_query($cnx2,$query="SELECT * FROM city WHERE name = $city_ant")
                or die($die2);

                $row3=@pg_fetch_array($result3);

                if (!$result3=@pg_query($cnx2,"INSERT INTO city_history(cityid, init_date, end_date, usrid) VALUES ('{$row3["id"]}',  $city_ant_date, '$last_date', '{$row["id"]}')")) {
                        $error=("It has not been possible to insert the new city period for user '{$row["login"]}'from values ('{$row3["id"]}', $city_ant_date, '$last_date', '{$row["id"]}'). City name= $city_ant\n");
                        print ($error);
                }
            }

            if (is_null($row2["city"]))
                $city_ant = "NULL";
            else $city_ant = "'{$row2["city"]}'";

            $city_ant_date = "'{$row2["init"]}'";
        } elseif ($city_ant_date == "NULL")
            $city_ant_date = "'{$row2["init"]}'";
     }

     // We store the end date

     $last_date = $row2["_end"];

    } // while ending

    if ($historico)
    {

    // We have gone over every row, so we store in each history a new row with the latest value read, which lasts since the stored global date until
    // each specific stored date

        if (is_null($last_date))
            $last_date = "NULL";
        else
            $last_date = "'" . $last_date . "'";


        $result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = $area_ant")
         or die($die2);

        $row2=@pg_fetch_array($result2);

        if ($row2["id"] == NULL)
          {

            print ("Area with old id '$area_ant' does not exist. Area history for user '{$row["login"]}' and init date $last_date  will be assigned to area '$unidentified_area'.\n");

            $result2=@pg_query($cnx2,$query="SELECT * FROM area WHERE id_ant = '$unidentified_area'")
            or die($die2);

            $row2=@pg_fetch_array($result2);

          }

        if (!$result3=@pg_query($cnx2,"INSERT INTO area_history(areaid, init_date, end_date, usrid) VALUES ('{$row2["id"]}' , $area_ant_date, $last_date, '{$row["id"]}')")) {
            $error=("It has not been possible to insert the last area period for user '{$row["login"]}' from values ('{$row2["id"]}' ,  $area_ant_date, $last_date, '{$row["id"]}').\n");
                print ($error);
        }

        if (!$result3=@pg_query($cnx2,"INSERT INTO journey_history(journey, init_date, end_date, usrid) VALUES (" . DBPostgres::checkNull($journey_ant) . ", $journey_ant_date, $last_date, '{$row["id"]}')")) {
                $error=("It has not been possible to insert the last journey period for user '{$row["login"]}' from values ($journey_ant, $journey_ant_date, $last_date, '{$row["id"]}').\n");
                print ($error);
        }

        if ($hour_cost_ant == "NULL")
        {
            print "A NULL value for hour cost has been detected. It'll be changed to '0'.\n";
            $hour_cost_ant = "'0'";
        }

        if (!$result3=@pg_query($cnx2,"INSERT INTO hour_cost_history(hour_cost, init_date, end_date, usrid) VALUES (" . DBPostgres::checkNull($hour_cost_ant) . ", $hour_cost_ant_date, $last_date, '{$row["id"]}')")) {
                $error=("It has not been possible to insert the last hour cost period for user '{$row["login"]}' from values ($hour_cost_ant, $hour_cost_ant_date, $last_date, '{$row["id"]}').\n");
                print ($error);
        }

        $result3=@pg_query($cnx2,$query="SELECT * FROM city WHERE name = $city_ant")
            or die($die2);

        $row3=@pg_fetch_array($result3);

        if (!$result3=@pg_query($cnx2,"INSERT INTO city_history(cityid, init_date, end_date, usrid) VALUES ('{$row3["id"]}', $city_ant_date, $last_date, '{$row["id"]}')")) {
                $error=("It has not been possible to insert the last city period for user '{$row["login"]}' from values ('{$row3["id"]}', $city_ant_date, $last_date, '{$row["id"]}'). City name= $city_ant\n");
                print ($error);
        }
    }
}




// --------------------------------- COMPENSATIONS CONVERSION AND MIGRATION


// We get data about migrations in old table and translate it to a computation of the extra hours on its end date.

$result=@pg_query($cnx1,$query="SELECT * FROM compensation ORDER BY _end ASC")
or die($die1);

while ($row=@pg_fetch_array($result,NULL,PGSQL_ASSOC)) {

  $result2=@pg_query($cnx2,$query="SELECT * FROM usr WHERE login =" . DBPostgres::checkStringNull($row["uid"]))
  or die($die2);

  $row2=@pg_fetch_array($result2);

  $res=@pg_query($cnx2,$query="SELECT * FROM extra_hour WHERE usrid ='{$row2["id"]}' AND _date = '{$row["_end"]}'")
  or die($die2);

  if (pg_num_rows($res) == 0)
  {
    $action = new ExtraHoursReportAction(date_create("1900-01-01"), date_create($row["_end"]), $user);

    $report = $action->execute();

    $hours = $report[1][$row["uid"]]["total_extra_hours"] - $row["hours"];

    if (!$result3=@pg_query($cnx2,"INSERT INTO extra_hour(_date, hours, usrid) VALUES ('{$row["_end"]}', " . DBPostgres::checkNull($hours) . ", '{$row2["id"]}')")) {
            $error=("It has not been possible to insert the new extra hour for user '{$row2["login"]}' from values ('{$row["_end"]}', '$hours', '{$row2["id"]}') and from compensation ('{$row["uid"]}', '{$row["init"]}', '{$row["_end"]}', '{$row["hours"]}', '{$row["paid"]}').\n");
            print ($error);
    }
  }

}




// --------------------------------- DROPPING TEMPORARY COLUMNS

$result2=@pg_query($cnx2,"ALTER TABLE customer DROP COLUMN id_ant")
or die($die2);

$result2=@pg_query($cnx2,"ALTER TABLE sector DROP COLUMN id_ant")
or die($die2);

$result2=@pg_query($cnx2,"ALTER TABLE area DROP COLUMN id_ant")
or die($die2);

$result2=@pg_query($cnx2,"ALTER TABLE project DROP COLUMN id_ant")
or die($die2);


?>
