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

/** createCustomers web service.
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

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><customers><customer><name>Planet Express</name><type>medium</type><sectorId>1</sectorId><url>LOL</url></customer></customers>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'customers')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='createCustomers'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createCustomers'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "customer")
            {

                $customerVO = new CustomerVO();

                $parser->read();

                while ($parser->name != "customer") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "name":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $customerVO->setName(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "type":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $customerVO->setType(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "url":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $customerVO->setUrl(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "sectorId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $customerVO->setSectorId(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $createCustomers[] = $customerVO;

            }

        } while ($parser->read());

        if (count($createCustomers) >= 1)
            foreach((array)$createCustomers as $createCustomer)
            {
                if (CustomersFacade::CreateCustomer($createCustomer) == -1)
                {
                    $string = "<return service='createCustomers'><error id='1'>There was some error while updating the customers</error></return>";
                    break;
                }

            }

        if (!isset($string))
        {

            $string = "<return service='createCustomers'><ok>Operation Success!</ok><customers>";

            foreach((array) $createCustomers as $customer)
            {
                $string .= "<customer>" .
                  "<id>{$customer->getId()}</id>" .
                  "<sectorId>{$customer->getSectorId()}</sectorId>" .
                  "<name>" . escape_string($customer->getName()) . "</name>" .
                  "<type>{$customer->getType()}</type>" .
                  "<url>" . escape_string($customer->getUrl()) ."</url></customer>";
            }

            $string = $string . "</customers></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
