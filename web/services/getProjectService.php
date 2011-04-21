<?php
/*
 * Copyright (C) 2010 Igalia, S.L. <info@igalia.com>
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

/** getProject web service.
 *
 * This file runs the service to retrieve a project by its id. It is invoked
 * through GET with the following parameters:
 * <ul>
 *   <li>
 *     <b>pid</b> Id of the project to be retrieved.
 *   </li>
 *   <li>
 *     <b>sid</b> Id of the active session. This parameter is optional if we are
 *     in an environment which manages the sessions transparently, for example,
 *     a browser.
 *   </li>
 * </ul>
 * In case the operation is successful, it will return the data of the project
 * in a XML string formatted like this:
 * <code>
 * <?xml version="1.0"?>
 * <projects pid="1">
 *   <project>
 *     <id>1</id>
 *     <areaId>1</areaId>
 *     <activation>1</activation>
 *     <description>project 1</description>
 *     <invoice>2000</invoice>
 *     <initDate format="Y-m-d">2011-01-01</initDate>
 *     <endDate format="Y-m-d">2011-01-31</endDate>
 *     <estHours>100</estHours>
 *     <type>per_hours</type>
 *     <movedHours>0</movedHours>
 *     <schedType/>
 *   </project>
 * </projects>
 * </code>
 *
 * If there is not a project with that id, it returns an empty projects tag:
 * <code>
 * <?xml version="1.0"?>
 * <projects />
 * </code>
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');

    $projectId = $_GET['pid'];

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<projects";
            if ($projectId!="")
                $string = $string . " pid='" . $projectId . "'";
            $string = $string . "><error id='2'>You must be logged in</error></projects>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<projects";
            if ($projectId!="")
                $string = $string . " pid='" . $projectId . "'";
            $string = $string . "><error id='3'>Forbidden service for this User</error></projects>";
            break;
        }

        $project = false;
        if ($projectId) {
            $project = ProjectsFacade::GetProject($projectId);
        }
        $string = "<projects pid='" . $projectId . "'>";

        if ($project)
        {
          $string = $string . "<project><id>{$project->getId()}</id><areaId>{$project->getAreaId()}</areaId><activation>{$project->getActivation()}</activation><description>" . escape_string($project->getDescription()) . "</description><invoice>{$project->getInvoice()}</invoice>";

          if (!is_null($project->getInit()))
                $string = $string . "<initDate format='Y-m-d'>{$project->getInit()->format("Y-m-d")}</initDate>";
          else $string = $string . "<initDate/>";

          if (!is_null($project->getEnd()))
                $string = $string . "<endDate format='Y-m-d'>{$project->getEnd()->format("Y-m-d")}</endDate>";
          else $string = $string . "<endDate/>";

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
