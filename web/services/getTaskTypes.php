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

    // Hard-coded data
    $response['success'] = true;
    $response['records'] = array(
        array('value' => 'administration',  'displayText' => 'Administration'),
        array('value' => 'analysis',        'displayText' => 'Analysis'),
        array('value' => 'community',       'displayText' => 'Community'),
        array('value' => 'coordination',    'displayText' => 'Coordination'),
        array('value' => 'demonstration',   'displayText' => 'Demonstration'),
        array('value' => 'deployment',      'displayText' => 'Deployment'),
        array('value' => 'design',          'displayText' => 'Design'),
        array('value' => 'documentation',   'displayText' => 'Documentation'),
        array('value' => 'environment',     'displayText' => 'Environment'),
        array('value' => 'implementation',  'displayText' => 'Implementation'),
        array('value' => 'maintenance',     'displayText' => 'Maintenance'),
        array('value' => 'publication',     'displayText' => 'Publication'),
        array('value' => 'requirements',    'displayText' => 'Requirements'),
        array('value' => 'sales',           'displayText' => 'Sales'),
        array('value' => 'sys_maintenance', 'displayText' => 'Systems maintenance'),
        array('value' => 'teaching',        'displayText' => 'Teaching'),
        array('value' => 'technology',      'displayText' => 'Technology'),
        array('value' => 'test',            'displayText' => 'Test'),
        array('value' => 'training',        'displayText' => 'Training'),
        array('value' => 'traveling',       'displayText' => 'Traveling'),
    );

} while (False);

header('Content-type: application/json');

// make it into a proper Json document with header etc
$json = json_encode($response);

// output correctly formatted Json
echo $json;
