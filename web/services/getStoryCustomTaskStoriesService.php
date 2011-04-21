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

/** getStoryCustomTasksStories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');

    $storyId = $_GET['stid'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<taskStories stid='" . $storyId . "'><error id='2'>You must be logged in</error></taskStories>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<taskStories stid='" . $storyId . "'><error id='3'>Forbidden service for this User</error></taskStories>";
            break;
        }

        $taskStories = CoordinationFacade::GetStoryCustomTaskStories($storyId);

            $string = "<taskStories stid='" . $storyId . "'>";


        foreach((array) $taskStories as $taskStory)
        {

        $string = $string . "<taskStory><id>{$taskStory->getId()}</id><risk>{$taskStory->getRisk()}</risk><name>{$taskStory->getName()}</name><estHours>{$taskStory->getEstHours()}</estHours><spent>{$taskStory->getSpent()}</spent><toDo>{$taskStory->getToDo()}</toDo><developer>";

        $developer = $taskStory->getDeveloper();

        if ($developer)
            $string = $string . "<id>{$developer->getId()}</id><login>{$developer->getLogin()}</login>";

        $string = $string . "</developer><reviewer>";

        $reviewer = $taskStory->getReviewer();

        if ($reviewer)
            $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

        $string = $string . "</reviewer>";

        if (!is_null($taskStory->getInit()))
            $string = $string . "<initDate format='Y-m-d'>{$taskStory->getInit()->format("Y-m-d")}</initDate>";
        else    $string = $string . "<initDate/>";

        if (!is_null($taskStory->getEnd()))
            $string = $string . "<endDate format='Y-m-d'>{$taskStory->getEnd()->format("Y-m-d")}</endDate>";
        else    $string = $string . "<endDate/>";

        if (!is_null($taskStory->getEstEnd()))
            $string = $string . "<estEndDate format='Y-m-d'>{$taskStory->getEstEnd()->format("Y-m-d")}</estEndDate>";
        else    $string = $string . "<estEndDate/>";

        if (!is_null($taskStory->getTaskSection()))
            $string = $string . "<taskSection><id>{$taskStory->getTaskSection()->getId()}</id><name>{$taskStory->getTaskSection()->getName()}</name></taskSection>";
        else    $string = $string . "<taskSection/>";

        $string = $string . "</taskStory>";

        }

        $string = $string . "</taskStories>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
