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

/** getUserStoryReport JSON web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');

    $init = $_GET['init'];

    $end = $_GET['end'];

    $dateFormat = $_GET['dateFormat'] && NULL;

    $sid = $_GET['sid'] && NULL;

    do {

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

        if ($dateFormat=="")
            $dateFormat = "Y-m-d";

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


        $report = TasksFacade::GetGlobalUsersStoriesReport($init, $end);

        $count = 0;

        $totalHours['total'] = 0;

        foreach((array) $report as $userLogin => $report2)
        {
            $count += count($report2);

            $record = array();

            $totalHours[$story] = 0;
            $record['userLogin'] = $userLogin;

            foreach((array) $report2 as $story => $hours)
            {
                $stories[$story] = true;
                $record[str_replace(".", ",", $story)] = round($hours, 2, PHP_ROUND_HALF_DOWN);
                $totalHours[$userLogin] += round($hours, 2, PHP_ROUND_HALF_DOWN);
                $totalHours['total'] += round($hours, 2, PHP_ROUND_HALF_DOWN);

            }

            $record['total'] = $totalHours[$userLogin];

            $records[] = $record;
        }

        foreach($records as $record)
        {
            $record['percentage'] = round(100 * $totalHours[$record['userLogin']]/$totalHours['total'], 3, PHP_ROUND_HALF_DOWN);
            $response['records'][] = $record;
        }

        $response['total'] = $count;

        $metaData['totalProperty'] = "total";
        $metaData['root'] = "records";
        $metaData['id'] = "story";

        $metaData['sortInfo']['field'] = 'userLogin';
        $metaData['sortInfo']['direction'] = 'ASC';

        $field['name'] = "userLogin";
        $field['type'] = "string";

        $metaData['fields'][] = $field;

        $field['name'] = "total";
        $field['type'] = "float";

        $metaData['fields'][] = $field;

        $field['name'] = "percentage";
        $field['type'] = "float";

        $metaData['fields'][] = $field;

        $column['header'] = "User Login";
        $column['dataIndex'] = "userLogin";
        $column['sortable'] = true;
        $column['width'] = 90;

        $response['columns'][] = $column;

        $column['width'] = 60;

        $field['type'] = "float";

        foreach((array)$stories as $name => $dumber)
        {
            $field['name'] = str_replace(".", ",", $name);
            $metaData['fields'][] = $field;

            $column['header'] = $name;
            $column['dataIndex'] = str_replace(".", ",", $name);
            $response['columns'][] = $column;
        }


        $column['header'] = "Total";
        $column['dataIndex'] = "total";
        $column['sortable'] = true;
        $column['width'] = 75;

        $response['columns'][] = $column;

        $column['header'] = "%";
        $column['dataIndex'] = "percentage";
        $column['sortable'] = true;
        $column['width'] = 60;

        $response['columns'][] = $column;

        $response['metaData'] = $metaData;

        $response['success'] = true;

    } while (False);

   // make it into a proper Json document with header etc
    $json = json_encode($response);

   // output correctly formatted Json
    echo $json;
