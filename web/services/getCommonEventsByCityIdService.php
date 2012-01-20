<?php
/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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

/** getProjectCustomerReport XML web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');

    $cityId = $_GET['cityId'];

    $dateFormat = $_GET['dateFormat'];

    do {

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<return service='getCommonEventsByCityId'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='getCommonEventsByCityId'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        if ($dateFormat=="")
            $dateFormat = "Y-m-d";

        if ($cityId != '') {
            $events = AdminFacade::GetCommonEventsByCityId($cityId);
            $string = "<return service='createCommonEvents'><commonEvents>";

            foreach($events as $commonEvent)
            {
                $string = $string . "<commonEvent><id>{$commonEvent->getId()}</id><cityId>{$commonEvent->getCityId()}</cityId><date format='Y-m-d'>{$commonEvent->getDate()->format('Y-m-d')}</date></commonEvent>";
            }

            $string = $string . "</commonEvents></return>";
        }
    } while (False);

    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

    // send an XML mime header
    header("Content-type: text/xml");

    // output correctly formatted XML
    echo $xml->asXML();
