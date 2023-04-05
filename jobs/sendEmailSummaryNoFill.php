<?php
/*
 * Copyright (C) 2018 Igalia, S.L. <info@igalia.com>
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

/** sendEmailAlertNoFill web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Pablo Abelenda
 */

    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    define('PHPREPORT_ROOT', __DIR__ . '/../');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
    $NO_FILL_EMAIL_FROM = ConfigurationParametersManager::getParameter('NO_FILL_EMAIL_FROM');
    $NO_FILL_TEMPLATE_MANAGERS = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_MANAGERS');
    $NO_FILL_SUBJECT_MANAGERS = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_MANAGERS');

    $today = new DateTime(date('Y-m-d'));
    $initDate = new DateTime($today->format('Y').'-01-01');

    $users = UsersFacade::GetAllActiveUsers();
    $excludedUsers = [];
    $criticalWarnedUsers = [];
    $lastWarnedUsers = [];

    $lastMonday = new DateTime(date("Y-m-d", strtotime("last week monday")));
    $lastFriday = new DateTime(date("Y-m-d", strtotime("last week friday")));

    $monday2weeksAgo = new DateTime(date("Y-m-d", strtotime("last week monday -1 week")));
    $friday2weeksAgo = new DateTime(date("Y-m-d", strtotime("last week friday -1 week")));

    $monday3weeksAgo = new DateTime(date("Y-m-d", strtotime("last week monday -2 weeks")));
    $friday3weeksAgo = new DateTime(date("Y-m-d", strtotime("last week friday -2 weeks")));

    $warnedUsers = [];
    $criticalUsers = [];
    $blockedUsers = [];

    foreach ($users as $user) {
        if (! in_array($user->getLogin(), $excludedUsers)) {
            $period = UsersFacade::GetUserJourneyHistoriesByIntervals($monday3weeksAgo, $lastMonday, $user->getId());
            if (empty($period) || ($period[0]->getJourney() == 0)) {
                // User is not currently hired or is on leave of absence, skip it.
                continue;
            }

            $report = UsersFacade::ExtraHoursReport($initDate, $today, $user);
            $emptyDaysLastWeek = TasksFacade::getEmptyDaysInPeriod($user, $lastMonday, $lastFriday);
            $emptyDays2WeeksAgo = TasksFacade::getEmptyDaysInPeriod($user, $monday2weeksAgo, $friday2weeksAgo);
            $emptyDays3WeeksAgo = TasksFacade::getEmptyDaysInPeriod($user, $monday3weeksAgo, $friday3weeksAgo);
            if (!empty($emptyDaysLastWeek) && ($period[0]->getInitDate() <= $lastMonday)) {
                array_push($warnedUsers, $user->getLogin());
            }
            if (!empty($emptyDays2WeeksAgo) && ($period[0]->getInitDate() <= $monday2weeksAgo)) {
                array_push($criticalUsers, $user->getLogin());
            }
            if (!empty($emptyDays3WeeksAgo) && ($period[0]->getInitDate() <= $monday3weeksAgo)) {
                array_push($blockedUsers, $user->getLogin());
            }
        }
    }

    $subject = $NO_FILL_SUBJECT_MANAGERS;
    $from = $NO_FILL_EMAIL_FROM;
    $to = $NO_FILL_EMAIL_FROM;
    $headers = '';
    $headers = $headers . "From: " . $from . "\r\n";
    $headers = $headers . "Reply-To: " . $from . "\r\n";
    $message = file_get_contents($NO_FILL_TEMPLATE_MANAGERS);

    $message = str_replace("###WEEK-3###", $monday3weeksAgo->format("W"), $message);
    $message = str_replace("###WEEK-2###", $monday2weeksAgo->format("W"), $message);
    $message = str_replace("###WEEK-1###", $lastMonday->format("W"), $message);
    if (empty($blockedUsers)) {
        $message = str_replace("###BLOCKED_PEOPLE###","Nobody on this list today :-)",$message);
    } else {
        $message = str_replace("###BLOCKED_PEOPLE###", implode("\r\n", $blockedUsers),$message);
    }

    if (empty($criticalUsers)) {
        $message = str_replace("###CRITICAL_PEOPLE###","Nobody on this list today :-)",$message);
    } else {
        $message = str_replace("###CRITICAL_PEOPLE###", implode("\r\n", $criticalUsers),$message);
    }

    if (empty($warnedUsers)) {
        $message = str_replace("###WARNED_PEOPLE###","Nobody on this list today :-D",$message);
    } else {
        $message = str_replace("###WARNED_PEOPLE###", implode("\r\n", $warnedUsers),$message);
    }

    mail($to, $subject, $message, $headers);
