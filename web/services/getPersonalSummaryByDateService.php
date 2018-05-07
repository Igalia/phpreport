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

/** getPersonalSummaryByDate web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');


    $date = $_GET['date'];

    $dateFormat = $_GET['dateFormat'];

    $sid = $_GET['sid'];

    if ($dateFormat=="")
        $dateFormat = "Y-m-d";

    if ($date == "")
        $date = new DateTime();
    else
    {
        $dateParse = date_parse_from_format($dateFormat, $date);

        $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";

        $date = date_create($date);
    }

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='2'>You must be logged in</error></personalSummary>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='3'>Forbidden service for this User</error></personalSummary>";
            break;
        }

        $userVO = $_SESSION['user'];

        $summary = TasksFacade::GetPersonalSummaryByLoginDate($userVO, $date);

        $dayHours = floor($summary['day']/60);
        $dayMinutes = $summary['day']-($dayHours*60);
        if ($dayMinutes < 10)
            $dayMinutes = "0" . $dayMinutes;
        $day = $dayHours . ":" . $dayMinutes;

        $weekHours = floor($summary['week']/60);
        $weekMinutes = $summary['week']-($weekHours*60);
        if ($weekMinutes < 10)
             $weekMinutes = "0" . $weekMinutes;
        $week = $weekHours . ":" . $weekMinutes;

        $weeklyGoalHours = floor($summary['weekly_goal']/60);
        $weeklyGoalMinutes = $summary['weekly_goal']-($weeklyGoalHours*60);
        if ($weeklyGoalMinutes < 10)
            $weeklyGoalMinutes = "0" . $weeklyGoalMinutes;
        $weekGoal = $weeklyGoalHours. ":" . $weeklyGoalMinutes;

        $initDate = new DateTime($date->format('Y').'-01-01');
        $extraHoursSummary = UsersFacade::ExtraHoursReport($initDate, $date, $userVO);

        $extraHours = $extraHoursSummary[1][$userVO->getLogin()]["extra_hours"];

        $hours = intval($extraHours);
        $minutes = round(((abs($extraHours) - abs($hours))*60),2);
        if ($minutes == 60) {
            $minutes = 0;
            $hours = $hours + 1 ;
        }
        if ($minutes < 10)
            $minutes = "0" . $minutes;
        $extraHours = $hours . ":" . $minutes;

        $accExtraHours = $extraHoursSummary[1][$userVO->getLogin()]["total_extra_hours"];

        $hours = intval($accExtraHours);
        $minutes = round(((abs($accExtraHours) - abs($hours))*60),2);
        if ($minutes == 60) {
            $minutes = 0;
            $hours = $hours + 1 ;
        }
        if ($minutes < 10)
            $minutes = "0" . $minutes;
        $accExtraHours = $hours . ":" . $minutes;

        $holidays = UsersFacade::GetPendingHolidayHours($initDate, $date, $userVO);
        $pendingHolidays = $holidays[$userVO->getLogin()];

        $hours = intval($pendingHolidays);
        $minutes = round(((abs($pendingHolidays) - abs($hours))*60),2);
        if ($minutes == 60) {
            $minutes = 0;
            $hours = $hours + 1 ;
        }
        if ($minutes < 10)
            $minutes = "0" . $minutes;

        $currentJourney = 8;
        $journeys = UsersFacade::GetUserJourneyHistoriesByIntervals($initDate, $date, $userVO->getId());
        if(count($journeys)==1) {
                $currentJourney = $journeys[0]->getJourney();
        }

        if ($hours > $currentJourney*7) {
            $days = intval($hours/$currentJourney);
            $hours = round($hours - ($days*$currentJourney),2);
            if ($hours == $currentJourney) {
                $hours = 0;
                $days = $days + 1;
            }
            if ($hours < 10) {
                $hours = "0" . $hours;
            }
        $pendingHolidays = intval($days) . " days " . intval($hours) . ":" . intval($minutes);
        } else {
            $pendingHolidays = intval($hours) . ":" . intval($minutes);
        }

        $string = "<personalSummary login='" . $userVO->getLogin() . "' date='" . $date->format($dateFormat) . "'><hours><day>" . $day  . "</day><week>" . $week  . "</week><weekly_goal>" . $weekGoal . "</weekly_goal><extra_hours>" . $extraHours . "</extra_hours><pending_holidays>" . $pendingHolidays . "</pending_holidays><acc_extra_hours>" . $accExtraHours . "</acc_extra_hours></hours></personalSummary>";

    } while(false);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
