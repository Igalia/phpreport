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

/** getProjectUserWeeklyHoursReport JSON web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Tony Thomas
 */

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/CustomersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

$projectId = $_GET['pid'];

$init = $_GET['init'] ?? "";

$end = $_GET['end'] ?? "";

$dateFormat = $_GET['dateFormat'] ?? "Y-m-d";

$sid = $_GET['sid'] ?? NULL;

do {

    $response['pid'] = $projectId;

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    if (!LoginManager::isLogged($sid))
    {
        if ($init!="")
            $response['init'] = $init;
        if ($end!="")
            $response['end'] = $end;
        $response['success'] = false;
        $error['id'] = 2;
        $error['message'] = "You must be logged in";
        $response['error'] = $error;
        break;
    }
    if (!LoginManager::isAllowed($sid))
    {
        if ($init!="")
            $response['init'] = $init;
        if ($end!="")
            $response['end'] = $end;
        $response['success'] = false;
        $error['id'] = 3;
        $error['message'] = "Forbidden service for this User";
        $response['error'] = $error;
        break;
    }

    if ($init!="")
    {
        $response['init'] = $init;

        $initParse = date_parse_from_format($dateFormat, $init);

        $init = "{$initParse['year']}-{$initParse['month']}-{$initParse['day']}";

        $init = date_create($init);

    } else $init = NULL;


    if ($end!="")
    {
        $response['end'] = $end;

        $endParse = date_parse_from_format($dateFormat, $end);

        $end = "{$endParse['year']}-{$endParse['month']}-{$endParse['day']}";

        $end = date_create($end);

    } else $end = NULL;

    $projectVO = new ProjectVO();

    $projectVO->setId($projectId);

    $report = TasksFacade::GetProjectUserWeeklyHoursReport($projectVO, $init, $end);

    $records = array();

    $weeklyRecords = array();

    $weekKeys = array();

    foreach((array) $report as $login => $totalHoursList ) {
        $record = array();
        $record['login'] = $login;
        foreach((array) $totalHoursList as $year => $weeklyHours)  {
            foreach( $weeklyHours as $week => $hours ) {
                $weeklyRecords[$year][$week] = true;
                $key = sprintf("%d-%02d", $year, $week); //e.g. 2020-01
                $record[$key] = round( $hours / 60, 2, PHP_ROUND_HALF_DOWN );
                if(!in_array($key, $weekKeys))
                    $weekKeys[] = $key;
            }
        }

        $records[$login] = $record;
    }

    // CSV formatter
    if(isset($_GET["format"]) && $_GET["format"] == "csv")
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="weekly.csv"');

        // do not cache the file
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');

        // header contains column names, first is "login"
        sort($weekKeys);
        array_unshift($weekKeys, "login");
        fputcsv($file, $weekKeys);

        // generate an empty row with all columns to zero
        $emptyRow = array_fill_keys($weekKeys, 0);
        // ensure rows are ordered by login
        ksort($records);
        // merge every data row with the empty row and output them
        foreach ($records as $row)
            fputcsv($file, array_replace($emptyRow, $row));

        // break execution here
        exit();
    }

    foreach($records as $record)
    {
        $response['records'][] = $record;
    }

    $metaData['totalProperty'] = "total";
    $metaData['root'] = "records";
    $metaData['id'] = "login";

    $metaData['sortInfo']['field'] = 'login';
    $metaData['sortInfo']['direction'] = 'ASC';

    $field['name'] = "login";
    $field['type'] = "string";

    $metaData['fields'][] = $field;

    $field['name'] = "week";
    $field['type'] = "string";

    $metaData['fields'][] = $field;


    $column['header'] = "Login";
    $column['dataIndex'] = "login";
    $column['sortable'] = true;
    $column['width'] = 100;


    $response['columns'][] = $column;
    $field['type'] = "float";

    // The weeks should show up in ascending order, sorted by week number
    ksort($weeklyRecords);
    foreach ($weeklyRecords as $year => $weeklyRecord ) {
        ksort( $weeklyRecord );
        foreach ( (array) $weeklyRecord as $week => $dumber ) {
            $field['name'] = sprintf("%d-%02d", $year, $week);
            $metaData['fields'][] = $field;

            $column['header'] = "Week $week, $year";
            $column['dataIndex'] = $field['name'];
            $response['columns'][] = $column;
        }
    }

    $response['metaData'] = $metaData;

    $response['success'] = true;

} while (false);

// make it into a proper Json document with header etc
$json = json_encode($response);

// output correctly formatted Json
echo $json;
