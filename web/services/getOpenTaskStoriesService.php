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

/** getOpenTaskStories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');

    $sid = $_GET['sid'];

    $projectId = $_GET['pid'];

    if (strtolower($_GET['uidActive']) == 'true')
        $userIdActive = true;
    else
        $userIdActive = false;


    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<taskStories";
            if ($projectId != "")
                $string = $string . " pid='" . $projectId . "'";
            if ($userIdActive)
                $string = $string . " uidActive = 'True'";
            else
                $string = $string . " uidActive = 'False'";
            $string = $string . "><error id='2'>You must be logged in</error></taskStories>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<taskStories";
            if ($projectId != "")
                $string = $string . " pid='" . $projectId . "'";
            if ($userIdActive)
                $string = $string . " uidActive = 'True'";
            else
                $string = $string . " uidActive = 'False'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></taskStories>";
            break;
        }

        if ($userIdActive)
            $userId = $_SESSION['user']->getId();

        $taskStories = CoordinationFacade::GetOpenTaskStories($userId, $projectId);

        $string = "<taskStories";
        if ($projectId != "")
            $string = $string . " pid='" . $projectId . "'";
        if ($userIdActive)
            $string = $string . " uidActive = 'True'>";
        else
            $string = $string . " uidActive = 'False'>";


        foreach((array) $taskStories as $taskStory)
        {

            $story = CoordinationFacade::GetStory($taskStory->getStoryId());

            $iteration = CoordinationFacade::GetIteration($story->getIterationId());

            $string = $string . "<taskStory><id>{$taskStory->getId()}</id><friendlyName>{$taskStory->getName()} - {$story->getName()} - {$iteration->getName()}</friendlyName></taskStory>";

        }

        $string = $string . "</taskStories>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
