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

/** getExtraHours web service.
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

    $calculatePendingHolidays = false;

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
        fputcsv($csvFile, array("Login","Extra Hours","Workable Hours",
                "Worked Hours","Total Extra Hours","Last task date", "Pending holiday hours"));

        // template with all values set to zero and the keys in the expected column order
        $templateRow = array_fill_keys(array("login","extra_hours","workable_hours",
                "total_hours","total_extra_hours","last_task_date", "pendingHolidayHours"), 0);
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

        if (isset($_GET['calculatePendingHolidays'])) {
            $calculatePendingHolidays = filter_var($_GET['calculatePendingHolidays'], FILTER_VALIDATE_BOOLEAN);
            // Pending holidays must be calculated from the beginning of the year,
            // otherwise the figure is not useful for users
            $initYearDate = new DateTime($init->format('Y').'-01-01');
        }

        $string = "<reports>";

        if ($userLogin != "")
        {
            $userVO = new UserVO();
            $userVO->setLogin($userLogin);

            $report = UsersFacade::ExtraHoursReport($init, $end, $userVO);
            if ($calculatePendingHolidays)
                $pendingHolidaysReport = UsersFacade::GetPendingHolidayHours($initYearDate, $end, $userVO);

        } else
        {
            $report = UsersFacade::ExtraHoursReport($init, $end);
            if ($calculatePendingHolidays)
                $pendingHolidaysReport = UsersFacade::GetPendingHolidayHours($initYearDate, $end);

            $string = $string
                . "<global><totalHours>{$report[0]["total_hours"]}</totalHours>"
                . "<workableHours>{$report[0]["workable_hours"]}</workableHours>"
                . "<extraHours>{$report[0]["extra_hours"]}</extraHours>"
                . "<totalExtraHours>{$report[0]["total_extra_hours"]}</totalExtraHours>"
                . "<lastTaskDate format=\"Y-m-d\">{$report[0]["last_task_date"]->format('Y-m-d')}</lastTaskDate>"
                . "</global>";
        }

        $string = $string . "<individual>";

        foreach((array) $report[1] as $login => $entry)
        {
            $entry["last_task_date"] = $entry["last_task_date"]->format('Y-m-d');
            if ($calculatePendingHolidays)
                $entry["pendingHolidayHours"] = $pendingHolidaysReport[$login];

            if($csvExport) {
                $entry["login"] = $login;
                fputcsv($csvFile, array_replace($templateRow, $entry));
            }
            else {
                $string = $string
                    . "<report login='{$login}'>"
                    . "<totalHours>{$entry["total_hours"]}</totalHours>"
                    . "<workableHours>{$entry["workable_hours"]}</workableHours>"
                    . "<extraHours>{$entry["extra_hours"]}</extraHours>"
                    . "<totalExtraHours>{$entry["total_extra_hours"]}</totalExtraHours>"
                    . "<lastTaskDate format=\"Y-m-d\">{$entry["last_task_date"]}</lastTaskDate>";
                if ($calculatePendingHolidays)
                    $string .= "<pendingHolidayHours>{$entry["pendingHolidayHours"]}</pendingHolidayHours>";
                $string .= "</report>";
            }
        }

        $string = $string . "</individual></reports>";

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
