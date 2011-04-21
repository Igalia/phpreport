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

/** getUsersProjectsReport web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    $init = $_GET['init'];

    $end = $_GET['end'];

    $dateFormat = $_GET['dateFormat'];

    $uid = $_GET['uid'];

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='2'>You must be logged in</error></report>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<report";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></report>";
            break;
        }

        $string = "<workedHours";
        if ($init!="")
                $string .= " init='" . $init . "'";
        if ($end!="")
                $string .= " end='" . $end . "'";
        $string .= ">";

        if ($dateFormat=="")
        $dateFormat = "Y-m-d";

        if ($init!="")
        {
        $initParse = date_parse_from_format($dateFormat, $init);

            $init = "{$initParse['year']}-{$initParse['month']}-{$initParse['day']}";
        } else
            $init = "1900-01-01";

        $init = date_create($init);

        if ($end!="")
        {
        $endParse = date_parse_from_format($dateFormat, $end);

            $end = "{$endParse['year']}-{$endParse['month']}-{$endParse['day']}";

            $end = date_create($end);
        } else
            $end = new DateTime();

        if (is_null($uid))
            $report = TasksFacade::GetGlobalUsersProjectsReport($init, $end);
        else
        {
            $user = new UserVO();
            $user->setLogin($uid);
            $report = TasksFacade::GetUserProjectReport($user, $init, $end);
        }

        foreach((array) $report as $userLogin => $entry)
        {
            $total = 0;
            $string = $string . "<user login='" . escape_string($userLogin) . "'>";
            foreach((array) $entry as $project => $hours)
            {
                $string = $string . "<project name='" . escape_string($project) . "'>$hours</project>";
                $total += $hours;
            }
            $string = $string . "<total>$total</total>";
            $string = $string . "</user>";
        }

        $string = $string . "</workedHours>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
