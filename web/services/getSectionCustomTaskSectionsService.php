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

/** getSectionCustomTaskSections web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');

    $sectionId = $_GET['scid'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<taskSections stid='" . $sectionId . "'><error id='2'>You must be logged in</error></taskSections>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<taskSections stid='" . $sectionId . "'><error id='3'>Forbidden service for this User</error></taskSections>";
            break;
        }

        $taskSections = CoordinationFacade::GetSectionCustomTaskSections($sectionId);

        $string = "<taskSections stid='" . $sectionId . "'>";


        foreach((array) $taskSections as $taskSection)
        {

        $string = $string . "<taskSection><id>{$taskSection->getId()}</id><risk>{$taskSection->getRisk()}</risk><name>{$taskSection->getName()}</name><estHours>{$taskSection->getEstHours()}</estHours><spent>{$taskSection->getSpent()}</spent><toDo>{$taskSection->getToDo()}</toDo><developer>";

        $developer = $taskSection->getDeveloper();

        if ($developer)
            $string = $string . "<id>{$developer->getId()}</id><login>{$developer->getLogin()}</login>";

        $string = $string . "</developer><reviewer>";

        $reviewer = $taskSection->getReviewer();

        if ($reviewer)
            $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

        $string = $string . "</reviewer>";

        $string = $string . "</taskSection>";

        }

        $string = $string . "</taskSections>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
