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

/** getUserTasks web service.
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

    $userLogin = $_GET['uid'];

    $date = $_GET['date'];

    $dateFormat = $_GET['dateFormat'];

    $login = $_GET['login'];

    if ($userLogin=="")
        $userLogin= $login;

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
            $string = "<tasks uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='2'>You must be logged in</error></tasks>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<tasks uid='" . $userLogin . "' date='" . $date->format($dateFormat) . "'><error id='3'>Forbidden service for this User</error></tasks>";
            break;
        }

        $userVO = new UserVO();

        $userVO->setLogin($userLogin);

        $tasks = TasksFacade::GetUserTasksByLoginDate($userVO, $date);

        $string = "<tasks login='" . $userLogin . "' date='" . $date->format($dateFormat) . "'>";

        foreach((array) $tasks as $task)
        {

            $string = $string . "<task><id>{$task->getId()}</id><date format='$dateFormat'>{$task->getDate()->format($dateFormat)}</date><initTime>" . str_pad(floor($task->getInit()/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($task->getInit()%60, 2, "0", STR_PAD_LEFT)  . "</initTime><endTime>" . str_pad(floor($task->getEnd()/60)%24, 2, "0", STR_PAD_LEFT) . ":" . str_pad($task->getEnd()%60, 2, "0", STR_PAD_LEFT)  . "</endTime><story>" . escape_string($task->getStory()) . "</story><telework>";

            if ($task->getTelework())
                $string = $string . "true";
            else $string = $string . "false";
            $string = $string . "</telework><onsite>";

            if ($task->getOnsite())
                $string = $string . "true";
            else $string = $string . "false";

            $string .= "</onsite><ttype>" . escape_string($task->getTtype()) . "</ttype><text>" . escape_string($task->getText()) . "</text><phase>" . escape_string($task->getPhase()) . "</phase><userId>{$task->getUserId()}</userId><projectId>{$task->getProjectId()}</projectId><customerId>{$task->getCustomerId()}</customerId><taskStoryId>{$task->getTaskStoryId()}</taskStoryId></task>";

        }

        $string = $string . "</tasks>";

    } while(false);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
