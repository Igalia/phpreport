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


require_once("include/util.php");

function getCnx() {

    $connectionString = "host=localhost port=15435 user=phpreport dbname=phpreport password=phpreport";

    $cnx = pg_connect($connectionString);
     if ($cnx == NULL) throw new DBConnectionErrorException($connectionString);

    return $cnx;

}

function getValidUsers($init, $end) {

    $cnx = getCnx();

    // This SQL Sentence is valid for testing every value returned if we don't care about compensations. See ExtraHoursReportActionTest.php
    //$validUsers=@pg_exec($cnx,$query="SELECT uid FROM users WHERE staff='t' AND  NOT (uid) IN (SELECT DISTINCT uid FROM compensation) AND  NOT (uid) IN (SELECT DISTINCT uid FROM extra_hours WHERE date>='". $init . "' AND date<='". $end . "') ORDER by uid") or die($die);

    // This SQL Sentence is valid for testing compensations migration if we don't care about testing only the total number of extra hours (YOU MUST MAKE A CHOICE!). See ExtraHoursReportActionTest.php
    $validUsers=@pg_exec($cnx,$query="SELECT uid FROM users WHERE staff='t' AND  NOT (uid) IN (SELECT DISTINCT uid FROM extra_hours WHERE date>='". $init . "' AND date<='". $end . "') AND NOT (uid) IN (SELECT DISTINCT uid FROM compensation WHERE init < '" . $init . "' AND _end >= '" . $init . "' AND _end <= '" . $end ."') ORDER by uid") or die($die);
    $validUsersResult=array();
    for ($i=0;$row=@pg_fetch_array($validUsers,$i,PGSQL_ASSOC);$i++)
      $validUsersResult[]=$row["uid"];

    return $validUsersResult;
}

function oldExtraHours($init, $end) {

    $sheets = 6;

    $cnx = getCnx();

    $users=@pg_exec($cnx,$query="SELECT uid FROM users WHERE staff='t' ORDER by uid")
      or die($die);
    $users_consult=array();
    for ($i=0;$row=@pg_fetch_array($users,$i,PGSQL_ASSOC);$i++)
      $users_consult[]=$row["uid"];

    // Select the holiday hours spent in the selected year
    $year=substr($init,0,4);
    $lowest_date=$year."-01-01";
    $uppest_date=$year."-12-31";
    $holiday_hours=@pg_exec($cnx,$query="SELECT uid, SUM( _end - init ) / 60.0 AS add_hours FROM task WHERE ( _date >= '"
           .$lowest_date."'::date AND _date <= '".$uppest_date."'::date ) AND type='vac' "
           ."GROUP BY uid ORDER BY uid ASC")
      or die($die);
    $holiday_hours_consult=array();
    for ($i=0;$row=@pg_fetch_array($holiday_hours,$i,PGSQL_ASSOC);$i++) {
      $holiday_hours_consult[$row["uid"]]=$row;
    }
    @pg_freeresult($holiday_hours);

    $worked_hours_consult=net_extra_hours($cnx,$init,$end);

    // Select most recent overriden extra hours before
    // the init date for each user
    //Remember $init=$end in this case
    $extra_hours=@pg_exec($cnx,$query="SELECT e.uid,e.hours,e.date
      FROM extra_hours AS e JOIN (
        SELECT uid,MAX(date) AS date FROM extra_hours
        WHERE date<'$init'
        GROUP BY uid) AS m ON (e.uid=m.uid AND e.date=m.date)
      WHERE e.uid IN (SELECT uid FROM users WHERE staff='t')")
      or die($die);
    $extra_hours_consult=array();
    for ($i=0;$row=@pg_fetch_array($extra_hours,$i,PGSQL_ASSOC);$i++) {
      $extra_hours_consult[$row["uid"]]=$row;
    }
    @pg_freeresult($extra_hours);

    // Let's compute the accumulated extra hours before the period we're computing now
    foreach ($users_consult as $k) {
      // $k is the uid

      // If there are forced extra hours defined before init, take them into account
      // and only compute hours after the defined ones, but before the init of the
      // period we're computing
      if (!empty($extra_hours_consult[$k])) {
        $previous_init=date_web_to_sql(day_day_moved(date_sql_to_web($extra_hours_consult[$k]["date"]),1));
        $previous_hours=$extra_hours_consult[$k]["hours"];
      } else {
        $previous_init="1900-01-01";
        $previous_hours=0;
      }

      $h=net_extra_hours($cnx,$previous_init,
        date_web_to_sql(day_yesterday(date_sql_to_web($init))),$k);
      if (!empty($h[$k]["extra_hours"])) $previous_hours+=$h[$k]["extra_hours"];

      // Put them all
      $worked_hours_consult[$k]["total_extra_hours"]=$worked_hours_consult[$k]["extra_hours"]
        +$previous_hours;
      $worked_hours_consult[$k]["pending_holiday_hours_year"]=YEARLY_HOLIDAY_HOURS-$holiday_hours_consult[$k]["add_hours"];
    }

    return $worked_hours_consult;
  }

/*//Uncomment these lines in order to do a simple test of the functions

$init = "2007-01-01";
$end = "2007-12-31";

$oldUsersResults = oldExtraHours($init, $end);

$users = array("chema");

$users = getValidUsers($init, $end);

$init = date_create($init);
$end = date_create($end);


foreach($users as $k)
{
    print "\nUser: " . $k . "\n";
    print "Worked: " . $oldUsersResults[$k]["total_hours"] . "\n";
    print "Workable hours: " . $oldUsersResults[$k]["workable_hours"] . "\n";
    print "Extra hours: " . $oldUsersResults[$k]["extra_hours"] . "\n";
    print "Total extra hours: " . $oldUsersResults[$k]["total_extra_hours"] . "\n";

}*/
