<?php
/*
 * Copyright (C) 2022 Igalia, S.L. <info@igalia.com>
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

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

$sid = $_GET['sid'] ?? NULL;

do {
    $response = array();

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

    $taskTypes = makeAPIRequest("/v1/timelog/task_types/");
    if (array_key_exists('token_refresh_error', $taskTypes)) {
        $response['success'] = false;
        $response['error'] = 'token_refresh_error';
        $error['message'] = "You must be logged in";
        break;
    }
    $response['success'] = true;
    $response['records'] = array_map(function ($item)
    {
        return array(
            'value' => $item->slug,
            'displayText' => $item->name,
            'active' => $item->active
        );
    }, $taskTypes);

} while (False);

header('Content-type: application/json');

// make it into a proper Json document with header etc
$json = json_encode($response);

// output correctly formatted Json
echo $json;
