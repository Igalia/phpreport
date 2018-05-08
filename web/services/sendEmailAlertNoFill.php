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

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
    $ALL_USERS_GROUP = ConfigurationParametersManager::getParameter('ALL_USERS_GROUP');
    $NO_FILL_CC_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_CC_CRITICAL');
    $NO_FILL_TEMPLATE_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_CRITICAL');
    $NO_FILL_SUBJECT_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_CRITICAL');
    $NO_FILL_DAYS_TRIGGER_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_DAYS_TRIGGER_CRITICAL');
    $NO_FILL_CC_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_CC_WARNING');
    $NO_FILL_TEMPLATE_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_WARNING');
    $NO_FILL_SUBJECT_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_WARNING');
    $NO_FILL_DAYS_TRIGGER_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_DAYS_TRIGGER_WARNING');

    function sendEmail($login, $lastTaskDate, $cc, $subject, $template) {
        $to = $login . "@domain.com";
        $subject = $subject;
        $message = file_get_contents($template);
        $message = str_replace("###LAST_TASK_DATE###",$lastTaskDate->format('Y-m-d'),$message);
        $message = str_replace("###LOGIN###",$login,$message);
        $headers = '';
        $headers = $headers . "From: <project-managers@domain.com>" . "\r\n";
        $headers = $headers . "Reply-To: <project-managers@domain.com>" . "\r\n";
        $headers = $headers . "Cc: " . $cc . "\r\n";

        mail($to, $subject, $message, $headers);
    }

    $today = new DateTime(date('Y-m-d'));
    $initDate = new DateTime($today->format('Y').'-01-01');

    $users = UsersFacade::GetAllUsers();

    foreach ($users as $user) {
        $groups = $user->getGroups();
        foreach ($groups as $group) {
            if ($group->getName() == $ALL_USERS_GROUP) {
                $report = UsersFacade::ExtraHoursReport($initDate, $today, $user);
                $login = $user->getLogin();
                $lastTaskDate = $report[1][$user->getLogin()]["last_task_date"];
                $difference = $lastTaskDate->diff($today);
                $difference = $difference->format('%a');
                if ($difference >= $NO_FILL_DAYS_TRIGGER_CRITICAL) {
                    $cc = $NO_FILL_CC_CRITICAL;
                    $subject = $NO_FILL_SUBJECT_CRITICAL;
                    $template = $NO_FILL_TEMPLATE_CRITICAL;
                    sendEmail($login, $lastTaskDate, $cc, $subject, $template);
                } elseif ($difference >= $NO_FILL_DAYS_TRIGGER_WARNING) {
                    $cc = $NO_FILL_CC_WARNING;
                    $subject = $NO_FILL_SUBJECT_WARNING;
                    $template = $NO_FILL_TEMPLATE_WARNING;
                    sendEmail($login, $lastTaskDate, $cc, $subject, $template);
                }
            }
        }
    }

