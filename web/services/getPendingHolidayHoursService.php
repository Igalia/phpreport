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

/** getPendingHolidayHours web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    $userLogin = $_GET['uid'] ?? "";

    $init = $_GET['init'] ?? "";

    $end = $_GET['end'] ?? "";

    $dateFormat = $_GET['dateFormat'] ?? "Y-m-d";

    $sid = $_GET['sid'] ?? NULL;

    $csvExport = (isset($_GET["format"]) && $_GET["format"] == "csv");
    $csvFile = null;
    if($csvExport)
    {
        // output headers so that the file is downloaded rather than displayed
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="weekly.csv"');

        // do not cache the file
        header('Pragma: no-cache');
        header('Expires: 0');

        $csvFile = fopen('php://output', 'w');

        // output header row
        fputcsv($csvFile, array("Login","Pending Holiday Hours"));
    }

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<report";
            if ($userLogin!="")
                $string = $string . " login='" . $userLogin . "'";
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
            if ($userLogin!="")
                $string = $string . " login='" . $userLogin . "'";
            if ($init!="")
                $string = $string . " init='" . $init . "'";
            if ($end!="")
                $string = $string . " end='" . $end . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></report>";
            break;
        }

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

        if ($userLogin != "")
        {
            $userVO = new UserVO();
            $userVO->setLogin($userLogin);

            $report = UsersFacade::GetPendingHolidayHours($init, $end, $userVO);

        } else
            $report = UsersFacade::GetPendingHolidayHours($init, $end);


        $string = "<report";
        if ($userLogin!="")
            $string = $string . " login='" . $userLogin . "'";
        if ($init!="")
            $string = $string . " init='" . $init->format($dateFormat) . "'";
        if ($end!="")
            $string = $string . " end='" . $end->format($dateFormat) . "'";
        $string = $string . ">";

        foreach((array) $report as $key => $entry)
        {
            if($csvExport)
                fputcsv($csvFile, array($key, $entry));
            else
                $string = $string . "<pendingHolidayHours login='{$key}'><hours>{$entry}</hours></pendingHolidayHours>";
        }

        $string = $string . "</report>";

    } while (False);

    if($csvExport) {
        // break execution here, do not output XML
        exit();
    }

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
