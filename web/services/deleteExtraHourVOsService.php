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

/** deleteExtraHourVOs web service.
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

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="utf-8"?><extraHours><extraHour><id>4</id></extraHour></extraHours>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'extraHours')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='deleteExtraHourVOs'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteExtraHourVOs'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "extraHour")
            {

                $extraHourVO = new ExtraHourVO();

                $parser->read();

                while ($parser->name != "extraHour") {

                    switch ($parser->name ) {

                        case "id":
                            $parser->read();
                            if ($parser->hasValue)
                            {
                                $extraHourVO->setId($parser->value);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        default:
                            $parser->next();
                            break;

                    }

                }

                $extraHoursList[] = $extraHourVO;

            }

        } while ($parser->read());

        if (count($extraHoursList) >= 1)
            foreach((array)$extraHoursList as $hour)
            {
                if (UsersFacade::DeleteExtraHour($hour) == -1)
                {
                    $string = "<return service='deleteExtraHourVOs'><error id='1'>There was some error while deleting the extra hour objects</error></return>";
                    break;
                }

            }

        if (!isset($string))
        {
            $string = "<return service='deleteExtraHourVOs'><ok>Operation Success!</ok></return>";
        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
