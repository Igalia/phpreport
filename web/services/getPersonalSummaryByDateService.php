<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

use Phpreport\Web\services\HolidayService;

define('PHPREPORT_ROOT', __DIR__ . '/../../');
require_once(PHPREPORT_ROOT . '/vendor/autoload.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

$dateString = $_GET['date'] ?? NULL;

$dateFormat = $_GET['dateFormat'] ?? "Y-m-d";

$sid = $_GET['sid'] ?? NULL;

$userLogin = $_GET['userLogin'] ?? NULL;

$date = new DateTime();
if ($dateString)
    $date = DateTime::createFromFormat($dateFormat, $dateString);

do {
    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    if (!LoginManager::isLogged($sid)) {
        $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='2'>You must be logged in</error></personalSummary>";
        break;
    }

    if (!LoginManager::isAllowed($sid)) {
        $string = "<personalSummary uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='3'>Forbidden service for this User</error></personalSummary>";
        break;
    }

    $userVO = $_SESSION['user'];
    if ($userLogin) {
        $userVO = UsersFacade::GetUserByLogin($userLogin);;
    }

    $summary = TasksFacade::GetPersonalSummaryByLoginDate($userVO, $date);

    $dayHours = floor($summary['day'] / 60);
    $dayMinutes = $summary['day'] - ($dayHours * 60);
    if ($dayMinutes < 10)
        $dayMinutes = "0" . $dayMinutes;
    $day = $dayHours . ":" . $dayMinutes;

    $weekHours = floor($summary['week'] / 60);
    $weekMinutes = $summary['week'] - ($weekHours * 60);
    if ($weekMinutes < 10)
        $weekMinutes = "0" . $weekMinutes;
    $week = $weekHours . ":" . $weekMinutes;

    $weeklyGoalHours = floor($summary['weekly_goal'] / 60);
    $weeklyGoalMinutes = $summary['weekly_goal'] - ($weeklyGoalHours * 60);
    if ($weeklyGoalMinutes < 10)
        $weeklyGoalMinutes = "0" . $weeklyGoalMinutes;
    $weekGoal = $weeklyGoalHours . ":" . $weeklyGoalMinutes;

    $initYearDate = new DateTime($date->format('Y') . '-01-01');
    $extraHoursSummary = UsersFacade::ExtraHoursReport($initYearDate, $date, $userVO);

    $currentJourney = 0;
    $journeys = UsersFacade::GetUserJourneyHistoriesByIntervals($date, $date, $userVO->getId());
    if (count($journeys) == 1) {
        $currentJourney = $journeys[0]->getJourney();
    }

    $extraHours = $extraHoursSummary[1][$userVO->getLogin()]["extra_hours"];
    $extraHours = HolidayService::formatHours($extraHours, $currentJourney, 5);

    $accExtraHours = $extraHoursSummary[1][$userVO->getLogin()]["total_extra_hours"];
    $accExtraHours = HolidayService::formatHours($accExtraHours, $currentJourney, 5);

    // Report holidays from the entire year, including those later than $date
    $endYearDate = new DateTime($date->format('Y') . '-12-31');
    $holidays = UsersFacade::GetHolidayHoursSummary($initYearDate, $endYearDate, $userVO, $date);
    $pendingHolidays = $holidays["pendingHours"][$userVO->getLogin()];
    $pendingHolidays = HolidayService::formatHours($pendingHolidays, $currentJourney, 5);

    $scheduledHolidays = $holidays["scheduledHours"][$userVO->getLogin()];
    $scheduledHolidays = HolidayService::formatHours($scheduledHolidays, $currentJourney, 5);

    $availableHolidays = $holidays["availableHours"][$userVO->getLogin()];
    $availableHolidays = HolidayService::formatHours($availableHolidays, $currentJourney, 5);

    $enjoyedHolidays = $holidays["enjoyedHours"][$userVO->getLogin()];
    $enjoyedHolidays = HolidayService::formatHours($enjoyedHolidays, $currentJourney, 5);

    $string = "<personalSummary login='" . $userVO->getLogin() . "' date='" . $date->format($dateFormat) . "'>"
        . "<hours><day>" . $day  . "</day>"
        . "<week>" . $week  . "</week>"
        . "<weekly_goal>" . $weekGoal . "</weekly_goal>"
        . "<extra_hours>" . $extraHours . "</extra_hours>"
        . "<pending_holidays>" . $pendingHolidays . "</pending_holidays>"
        . "<scheduled_holidays>" . $scheduledHolidays . "</scheduled_holidays>"
        . "<available_holidays>" . $availableHolidays . "</available_holidays>"
        . "<enjoyed_holidays>" . $enjoyedHolidays . "</enjoyed_holidays>"
        . "<acc_extra_hours>" . $accExtraHours . "</acc_extra_hours>"
        . "</hours></personalSummary>";
} while (false);

// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
