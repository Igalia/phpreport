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

/** deleteCommonEvents web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*
    $request = '<?xml version="1.0" encoding="utf-8"?>' .
        '<commonEvents>' .
            '<commonEvent>' .
                '<id>12</id>' .
            '</commonEvent>' .
            '<commonEvent>' .
                '<id>13</id>' .
            '</commonEvent>' .
        '</commonEvents>';
    */

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'commonEvents')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='deleteCommonEvents'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteCommonEvents'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "commonEvent")
            {

                $commonEventVO = new CommonEventVO();

                $parser->read();

                while ($parser->name != "commonEvent") {

                    switch ($parser->name) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $commonEventVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $deleteCommonEvents[] = $commonEventVO;

            }

        } while ($parser->read());


        if (count($deleteCommonEvents) >= 1)
            foreach((array)$deleteCommonEvents as $commonEvent)
            {
                if (AdminFacade::DeleteCommonEvent($commonEvent) == -1)
                {
                    $string = "<return service='deleteCommonEvents'><error id='1'>There was some error while deleting the common events</error></return>";
                    break;
                }
            }



        if (!$string)
            $string = "<return service='deleteCommonEvents'><ok>Operation Success!</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
