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
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
    $COMPANY_DOMAIN = ConfigurationParametersManager::getParameter('COMPANY_DOMAIN');
    $NO_FILL_EMAIL_FROM = ConfigurationParametersManager::getParameter('NO_FILL_EMAIL_FROM');
    $NO_FILL_CC_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_CC_CRITICAL');
    $NO_FILL_TEMPLATE_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_CRITICAL');
    $NO_FILL_SUBJECT_CRITICAL = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_CRITICAL');
    $NO_FILL_TRIGGER_CRITICAL_DAY = ConfigurationParametersManager::getParameter('NO_FILL_TRIGGER_CRITICAL_DAY');
    $NO_FILL_CC_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_CC_WARNING');
    $NO_FILL_TEMPLATE_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_WARNING');
    $NO_FILL_SUBJECT_WARNING = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_WARNING');
    $NO_FILL_TRIGGER_WARNING_DAY = ConfigurationParametersManager::getParameter('NO_FILL_TRIGGER_WARNING_DAY');
    $NO_FILL_CC_LAST = ConfigurationParametersManager::getParameter('NO_FILL_CC_LAST');
    $NO_FILL_TEMPLATE_LAST = ConfigurationParametersManager::getParameter('NO_FILL_TEMPLATE_LAST');
    $NO_FILL_SUBJECT_LAST = ConfigurationParametersManager::getParameter('NO_FILL_SUBJECT_LAST');
    $NO_FILL_TRIGGER_LAST_DAY = ConfigurationParametersManager::getParameter('NO_FILL_TRIGGER_LAST_DAY');

    /**
     * Function used te send emails to users, depending on the timing conditions
     * @param string $login: User login name
     * @param array $emptyDaysLastWeek: list of days that don't have any task during the previous week
     * @param string $cc: List of emails to be added in CC
     * @param string $subject: Subject of the email to be sent
     * @param string $template: Location of the email template
     * */
    function sendEmail($email, $emptyDaysLastWeek, $from, $cc, $subject, $template) {
        $to = $email;
        $login=explode("@", $email)[0];
        $message = file_get_contents($template);
        $message = str_replace("###LIST_OF_DATES###", implode(", ", $emptyDaysLastWeek),$message);
        $message = str_replace("###LOGIN###",$login,$message);
        $headers = '';
        $headers = $headers . "From: " . $from . "\r\n";
        $headers = $headers . "Reply-To: " . $from . "\r\n";
        $headers = $headers . "Cc: " . $cc . "\r\n";

        mail($to, $subject, $message, $headers);
    }

    $today = new DateTime(date('Y-m-d'));
    $initDate = new DateTime($today->format('Y').'-01-01');

    $users = UsersFacade::GetAllActiveUsers();
    $excludedUsers = [];

    foreach ($users as $user) {
        if (!in_array($user->getLogin(), $excludedUsers)) {
            $lastMonday = new DateTime(date("Y-m-d", strtotime("last week monday")));
            $lastFriday = new DateTime(date("Y-m-d", strtotime("last week friday")));

            $period = UsersFacade::GetUserJourneyHistoriesByIntervals($lastMonday, $lastMonday, $user->getId());
            if (empty($period) || ($period[0]->getJourney() == 0)) {
                // User is not currently hired or is on leave of absence, skip it.
                continue;
            }

            $login = $user->getLogin();
            $email = $login . "@" . $COMPANY_DOMAIN;
            $from = $NO_FILL_EMAIL_FROM;

            $emptyDaysLastWeek = TasksFacade::getEmptyDaysInPeriod($user, $lastMonday, $lastFriday);

            if (empty($emptyDaysLastWeek)) {
                // User doesn't have any empty day last week
                continue;
            }
            $dayOfWeek = $today->format('w');
            if ($dayOfWeek == $NO_FILL_TRIGGER_WARNING_DAY) {
                $cc = $NO_FILL_CC_WARNING;
                $subject = str_replace("###WEEK_NUMBER###", $lastMonday->format("W"), $NO_FILL_SUBJECT_WARNING);
                $template = $NO_FILL_TEMPLATE_WARNING;
                sendEmail($email, $emptyDaysLastWeek, $from, $cc,$subject, $template);
            } elseif ($dayOfWeek == $NO_FILL_TRIGGER_CRITICAL_DAY) {
                $cc = $NO_FILL_CC_CRITICAL;
                $subject = str_replace("###WEEK_NUMBER###", $lastMonday->format("W"), $NO_FILL_SUBJECT_CRITICAL);
                $template = $NO_FILL_TEMPLATE_CRITICAL;
                sendEmail($email, $emptyDaysLastWeek, $from, $cc,$subject, $template);
            } elseif ($dayOfWeek == $NO_FILL_TRIGGER_LAST_DAY) {
                $cc = $NO_FILL_CC_LAST;
                $subject = str_replace("###WEEK_NUMBER###", $lastMonday->format("W"), $NO_FILL_SUBJECT_LAST);
                $template = $NO_FILL_TEMPLATE_LAST;
                sendEmail($email, $emptyDaysLastWeek, $from, $cc,$subject, $template);
            }
        }
    }
