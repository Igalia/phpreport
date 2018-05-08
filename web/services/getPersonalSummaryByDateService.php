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

    /**
     * Function used to pretty print time. From hours to Days d hours:minutes
     * @param float $time: Time in hours to be converted
     * @param float $journey: Number of hours that represents a day in our life
     * @param int $limit: Number of days to change representation from hours to days
     * @return string formatedHours: String representing hours in human format
     */
    function formatHours ($time, $journey, $limit) {
        $negative = ($time < 0);
        $work_days = false;
        $time = abs($time);
        $minutes = intval($time * 60);
        $hours = intval($minutes / 60);
        $minutes = $minutes % 60;

        if ($time > $limit*$journey ) {
            $work_days = intval($hours / $journey);
            $more_minutes = intval(((abs(($work_days*$journey) - $hours)) - intval(abs(($work_days*$journey) - $hours)))*60);
            $minutes = $minutes + $more_minutes;
            $hours = intval(abs(($work_days*$journey) - $hours));
        }

        if ($hours < 10) {
            $hours = "0" . $hours;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }

        if ($work_days)
            $formatedHours = $work_days . " d " . $hours . ":" . $minutes;
        else
            $formatedHours = $hours . ":" . $minutes;

        if ($negative)
            $formatedHours = "-" . $formatedHours;

        return $formatedHours;
    }

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

        $currentJourney = 8;
        $journeys = UsersFacade::GetUserJourneyHistoriesByIntervals($initDate, $date, $userVO->getId());
        if(count($journeys)==1) {
                $currentJourney = $journeys[0]->getJourney();
        }

        $extraHours = $extraHoursSummary[1][$userVO->getLogin()]["extra_hours"];
        $extraHours = formatHours($extraHours, $currentJourney, 5);

        $accExtraHours = $extraHoursSummary[1][$userVO->getLogin()]["total_extra_hours"];
        $accExtraHours = formatHours($accExtraHours, $currentJourney, 5);

        $holidays = UsersFacade::GetPendingHolidayHours($initDate, $date, $userVO);
        $pendingHolidays = $holidays[$userVO->getLogin()];
        $pendingHolidays = formatHours($pendingHolidays, $currentJourney, 5);

        $string = "<personalSummary login='" . $userVO->getLogin() . "' date='" . $date->format($dateFormat) . "'><hours><day>" . $day  . "</day><week>" . $week  . "</week><weekly_goal>" . $weekGoal . "</weekly_goal><extra_hours>" . $extraHours . "</extra_hours><pending_holidays>" . $pendingHolidays . "</pending_holidays><acc_extra_hours>" . $accExtraHours . "</acc_extra_hours></hours></personalSummary>";

    } while(false);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
