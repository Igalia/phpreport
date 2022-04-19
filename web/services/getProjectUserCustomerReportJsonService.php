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

/** getProjectUserCustomerReport JSON web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
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

        if(!LoginManager::hasExtraPermissions($sid)) {
            $projectAssignedUsers = ProjectsFacade::GetProjectUsers( $projectId );
            $userCanViewProject = false;
            foreach($projectAssignedUsers as $userVO) {
                if($userVO->getLogin() == $_SESSION['user']->getLogin()) {
                    $userCanViewProject = true;
                    break;
                }
            }
            if(!$userCanViewProject) {
                $response['success'] = false;
                $error['id'] = 3;
                $error['message'] = "Forbidden service for this User";
                $response['error'] = $error;
                break;
            }
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

        $report = TasksFacade::GetProjectUserCustomerReport($projectVO, $init, $end);

        $count = count($report);

        $totalHours['total'] = 0;

        $records = array();

        foreach((array) $report as $login => $hours)
        {
            $record = array();

            $totalHours[$login] = 0;
            $record['login'] = $login;

            $totalHours[$login] += round($hours, 2, PHP_ROUND_HALF_DOWN);
            $totalHours['total'] += round($hours, 2, PHP_ROUND_HALF_DOWN);

            $record['total'] = $totalHours[$login];

            $records[] = $record;
        }

      foreach($records as $record) {
          $record['percentage'] = round(100 * $totalHours[$record['login']]/$totalHours['total'],
              2, PHP_ROUND_HALF_DOWN);
          $response['records'][] = $record;
      }

        $response['total'] = $count;

        $metaData['totalProperty'] = "total";
        $metaData['root'] = "records";
        $metaData['id'] = "login";

        $metaData['sortInfo']['field'] = 'login';
        $metaData['sortInfo']['direction'] = 'ASC';

        $field['name'] = "login";
                $field['type'] = "string";

        $metaData['fields'][] = $field;

        $field['name'] = "total";
                $field['type'] = "float";

        $metaData['fields'][] = $field;

        $field['name'] = "percentage";
                $field['type'] = "float";

        $metaData['fields'][] = $field;

        $column['header'] = "Login";
        $column['dataIndex'] = "login";
        $column['sortable'] = true;
        $column['width'] = 100;

        $response['columns'][] = $column;

        $field['type'] = "float";

        $column['header'] = "Total";
        $column['dataIndex'] = "total";
        $column['sortable'] = true;

                $response['columns'][] = $column;

        $column['header'] = "%";
        $column['dataIndex'] = "percentage";
        $column['sortable'] = true;

                $response['columns'][] = $column;

                $response['metaData'] = $metaData;

        $response['success'] = true;

    } while (False);

   // make it into a proper Json document with header etc
    $json = json_encode($response);

   // output correctly formatted Json
    echo $json;
