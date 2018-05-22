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
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
    $ALL_USERS_GROUP = ConfigurationParametersManager::getParameter('ALL_USERS_GROUP');
    $NO_FILL_EMAIL_FROM = ConfigurationParametersManager::getParameter('NO_FILL_EMAIL_FROM');
    $NO_FILL_TEMPLATE_MANAGERS = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_MANAGERS');
    $NO_FILL_SUBJECT_MANAGERS = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_MANAGERS');
    $NO_FILL_DAYS_TRIGGER_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_DAYS_TRIGGER_CRITICAL');
    $NO_FILL_DAYS_TRIGGER_LAST = ConfigurationParametersManager::getParameter('NO_FILL_DAYS_TRIGGER_LAST');

    $today = new DateTime(date('Y-m-d'));
    $initDate = new DateTime($today->format('Y').'-01-01');

    $users = UsersFacade::GetAllUsers();
    $excludedUsers = [];
    $criticalWarnedUsers = [];
    $lastWarnedUsers = [];

    foreach ($users as $user) {
        if (! in_array($user->getLogin(), $excludedUsers)) {
            $groups = $user->getGroups();
            foreach ($groups as $group) {
                if ($group->getName() == $ALL_USERS_GROUP) {
                    $report = UsersFacade::ExtraHoursReport($initDate, $today, $user);
                    $login = $user->getLogin();
                    $lastTaskDate = $report[1][$user->getLogin()]["last_task_date"];
                    $difference = $lastTaskDate->diff($today);
                    $difference = $difference->format('%a');
                    if ($difference >= $NO_FILL_DAYS_TRIGGER_LAST) {
                        array_push($lastWarnedUsers, $login);
                    } elseif ($difference >= $NO_FILL_DAYS_TRIGGER_CRITICAL) {
                        array_push($criticalWarnedUsers, $login);
                    }
                }
            }
        }
    }

    $subject = $NO_FILL_SUBJECT_MANAGERS;
    $from = $NO_FILL_EMAIL_FROM;
    $to = $NO_FILL_EMAIL_FROM;
    $headers = '';
    $headers = $headers . "From: " . $from . "\r\n";
    $headers = $headers . "Reply-To: " . $from . "\r\n";

    $criticalWarnedPeople = "";
    foreach($criticalWarnedUsers as $u)
        $criticalWarnedPeople = $criticalWarnedPeople . $u . "\r\n";
    $lastWarnedPeople = "";
    foreach($lastWarnedUsers as $u)
        $lastWarnedPeople = $lastWarnedPeople . $u . "\r\n";

    $message = file_get_contents($NO_FILL_TEMPLATE_MANAGERS);

    $message = str_replace("###NO_FILL_DAYS_TRIGGER_CRITICAL###",$NO_FILL_DAYS_TRIGGER_CRITICAL,$message);
    if ($criticalWarnedUsers == []) {
        $message = str_replace("###CRITICAL_PEOPLE###","Nobody on this list today :-)",$message);
    } else {
        $message = str_replace("###CRITICAL_PEOPLE###",$criticalWarnedPeople,$message);
    }

    if ($lastWarnedUsers == []) {
        $message = str_replace("###LAST_PEOPLE###","Nobody on this list today :-D",$message);
    } else {
        $message = str_replace("###LAST_PEOPLE###",$lastWarnedPeople,$message);
    }

    mail($to, $subject, $message, $headers);
