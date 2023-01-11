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

/** updateCities web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="utf-8"?><cities><city><id>1</id><name>City with new name</name></city><city><id>3</id><name>Another renamed city</name></city></cities>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'cities')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='updateCities'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateCities'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "city")
            {

                $cityVO = new CityVO();

                $parser->read();

                while ($parser->name != "city") {

                    switch ($parser->name ) {

                        case "name":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $cityVO->setName(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $cityVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $updateCities[] = $cityVO;

            }

        } while ($parser->read());


        if (count($updateCities) >= 1)
            foreach((array)$updateCities as $city)
            {
                if (AdminFacade::UpdateCity($city) == -1)
                {
                    $string = "<return service='updateCities'><error id='1'>There was some error while updating the cities</error></return>";
                    break;
                }

            }

        if (!isset($string))
        {

            $string = "<return service='updateCities'><ok>Operation Success!</ok><cities>";

            foreach((array) $updateCities as $city)
                $string = $string . "<city><id>{$city->getId()}</id><name>{$city->getName()}</name></city>";

            $string = $string . "</cities></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
