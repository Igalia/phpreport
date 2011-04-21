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

/** getAllProjects web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<projects><error id='2'>You must be logged in</error></projects>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<projects><error id='3'>Forbidden service for this User</error></projects>";
            break;
        }

        $projects = ProjectsFacade::GetAllProjects();

        $string = "<projects>";

        foreach((array) $projects as $project)
        {

            $string = $string . "<project><id>{$project->getId()}</id><areaId>{$project->getAreaId()}</areaId><activation>{$project->getActivation()}</activation><description>" . escape_string($project->getDescription()) . "</description><invoice>{$project->getInvoice()}</invoice>";

            if (!is_null($project->getInit()))
                $string = $string . "<init format='Y-m-d'>{$project->getInit()->format("Y-m-d")}</init>";
            else $string = $string . "<init/>";

            if (!is_null($project->getEnd()))
                $string = $string . "<end format='Y-m-d'>{$project->getEnd()->format("Y-m-d")}</end>";
            else $string = $string . "<end/>";

            $string = $string . "<estHours>{$project->getEstHours()}</estHours><type>" . escape_string($project->getType()) . "</type><movedHours>{$project->getMovedHours()}</movedHours><schedType>" . escape_string($project->getSchedType()) . "</schedType></project>";

        }

        $string = $string . "</projects>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
