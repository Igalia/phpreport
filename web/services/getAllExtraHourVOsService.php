<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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

/** getAllExtraHourVOs web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<extraHours><error id='2'>You must be logged in</error></extraHours>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<extraHours><error id='3'>Forbidden service for this User</error></extraHours>";
            break;
        }

        $extraHours = UsersFacade::GetAllExtraHours();

        $string = "<extraHours>";

        foreach((array) $extraHours as $extraHour)
            $string = $string . "<extraHour><id>{$extraHour->getId()}</id><hours>{$extraHour->getHours()}</hours><userId>{$extraHour->getUserId()}</userId><date format='Y-m-d'>{$extraHour->getDate()->format('Y-m-d')}</date></extraHour>";

        $string = $string . "</extraHours>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
