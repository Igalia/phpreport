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

/** getAllCustomers web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CustomersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');

    $sid = $_GET['sid'];

    if (isset($_GET['active']) && strtolower($_GET['active']) == "true")
        $active = True;
    else
        $active = False;

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<customers><error id='2'>You must be logged in</error></customers>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<customers><error id='3'>Forbidden service for this User</error></customers>";
            break;
        }

        $customers = CustomersFacade::GetAllCustomers($active);

        $string = "<customers>";

        foreach((array) $customers as $retrievedCustomer)
        {
            $sector = CustomersFacade::GetSector($retrievedCustomer->getSectorId());
            $string = $string . "<customer><id>{$retrievedCustomer->getId()}</id><name>{$retrievedCustomer->getName()}</name><type>{$retrievedCustomer->getType()}</type><url>{$retrievedCustomer->getUrl()}</url><sector>{$sector->getName()}</sector></customer>";
        }

        $string = $string . "</customers>";

    } while (False);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
