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

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');

$sid = $_GET['sid'];

do {
    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    $user = LoginManager::isLogged($sid);

    if (!$user)
    {
        $string = "<templates><error id='2'>You must be logged in</error></templates>";
        break;
    }

    if (!LoginManager::isAllowed($sid))
    {
        $string = "<templates><error id='3'>Forbidden service for this User</error></templates>";
        break;
    }

    $parameters[] = ConfigurationParametersManager::getParameter('DB_HOST');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_PORT');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_USER');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_NAME');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_PASSWORD');

    $connectionString = "host=$parameters[0] port=$parameters[1] user=$parameters[2] dbname=$parameters[3] password=$parameters[4]";
    $connect = pg_connect($connectionString);
    pg_set_error_verbosity($connect, PGSQL_ERRORS_VERBOSE);

    $sql = "select * from template where usrid={$user->getId()}";
    $res = pg_query($connect, $sql);

    $string = "<templates login='" . $user->getLogin() . "'>";

    if(pg_num_rows($res) > 0) {
        for($i = 0; $i < pg_num_rows($res); $i++) {

            $row = @pg_fetch_array($res);
            $string .= "<template><id>{$row['id']}</id>";
            $string .= "<story>" . escape_string($row['story']) . "</story>";
            $string .= "<ttype>" . escape_string($row['ttype']) . "</ttype>";
            $string .= "<name>" . escape_string($row['name']) . "</name>";
            $string .= "<text>" . escape_string($row['text']) . "</text>";
            $string .= "<userId>{$user->getId()}</userId>";
            $string .= "<projectId>{$row['projectid']}</projectId>";
            $string .= "<customerId>{$row['customerid']}</customerId>";
            $string .= "<taskStoryId>{$row['task_storyid']}</taskStoryId>";

            $string .= "<telework>";
            if (strtolower($row['telework']) == "t")
                $string .= "true";
            else
                $string .= "false";
            $string .= "</telework>";

            $string .= "<onsite>";
            if (strtolower($row['onsite']) == "t")
                $string .= "true";
            else
                $string .= "false";
            $string .= "</onsite></template>";
        }
    }
    $string .= "</templates>";

} while(false);

// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
