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

/** createExtraHourVOs web service.
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

    /*$request = '<?xml version="1.0" encoding="utf-8"?><extraHours><extraHour><hours>0</hours><userId>1</userId><date format="Y-m-d">2012-01-01</date></extraHour></extraHours>';*/

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
            $string = "<return service='createExtraHourVOs'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createExtraHourVOs'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "extraHour")
            {

                $extraHourVO = new ExtraHourVO();

                $parser->read();

                while ($parser->name != "extraHour") {

                    switch ($parser->name ) {

                        case "userId":
                            $parser->read();
                            if ($parser->hasValue)
                            {
                                $extraHourVO->setUserId($parser->value);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "hours":
                            $parser->read();
                            if ($parser->hasValue)
                            {
                                $extraHourVO->setHours($parser->value);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "date":
                            $dateFormat = $parser->getAttribute("format");
                            if (is_null($dateFormat))
                                $dateFormat = "Y-m-d";
                            $parser->read();
                            if ($parser->hasValue)
                            {
                                $date = $parser->value;
                                $dateParse = date_parse_from_format($dateFormat, $date);
                                $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                $extraHourVO->setDate(date_create($date));
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        default:
                            $parser->next();
                            break;

                    }

                }

                $createExtraHours[] = $extraHourVO;

            }

        } while ($parser->read());


        if (count($createExtraHours) >= 1)
            foreach((array)$createExtraHours as $hour)
            {
                if (UsersFacade::CreateExtraHour($hour) == -1)
                {
                    $string = "<return service='createExtraHourVOs'><error id='1'>There was some error while creating the extra hour objects</error></return>";
                    break;
                }

            }



        if (!$string)
        {

            $string = "<return service='createExtraHourVOs'><ok>Operation Success!</ok><extraHours>";

            foreach((array) $createExtraHours as $hour)
                $string = $string . "<extraHour><id>{$hour->getId()}</id><userId>{$hour->getUserId()}</userId><hours>{$hour->getHours()}</hours><date format='Y-m-d'>{$hour->getDate()->format('Y-m-d')}</date></extraHour>";

            $string = $string . "</extraHours></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
