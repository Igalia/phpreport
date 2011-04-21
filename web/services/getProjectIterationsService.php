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

/** getProjectIterations web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');

    $projectId = $_GET['pid'];

    $login = $_GET['login'];

    $sid = $_GET['sid'];


    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<iterations pid='" . $projectId . "'><error id='2'>You must be logged in</error></iterations>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<iterations pid='" . $projectId . "'><error id='3'>Forbidden service for this User</error></iterations>";
            break;
        }

        $iterations = CoordinationFacade::GetProjectIterations($projectId);

        $string = "<iterations pid='" . $projectId . "'>";


        foreach((array) $iterations as $iteration)
        {

        $string = $string . "<iteration><id>{$iteration->getId()}</id><name>{$iteration->getName()}</name><summary>{$iteration->getSummary()}</summary>";

        if (!is_null($iteration->getInit()))
            $string = $string . "<initDate format='Y-m-d'>{$iteration->getInit()->format("Y-m-d")}</initDate>";
        else    $string = $string . "<initDate/>";

        if (!is_null($iteration->getEnd()))
            $string = $string . "<endDate format='Y-m-d'>{$iteration->getEnd()->format("Y-m-d")}</endDate>";
        else    $string = $string . "<endDate/>";

        $string = $string . "</iteration>";

        }

        $string = $string . "</iterations>";

    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
