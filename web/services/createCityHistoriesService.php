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

/** createCityHistories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/CityHistoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><cityHistories><cityHistory><userId>81</userId><cityId>1</cityId><init format="Y-m-d">2009-06-01</init><end format="Y-m-d">2009-12-01</end></cityHistory><cityHistory><userId>81</userId><cityId>2</cityId><init format="Y-m-d">2009-01-01</init><end format="Y-m-d">2009-06-01</end></cityHistory></cityHistories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'cityHistories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='createCityHistories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createCityHistories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "cityHistory")
            {

                $cityHistoryVO = new CityHistoryVO();

                $userAssignGroups = array();

                $parser->read();

                while ($parser->name != "cityHistory") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "cityId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $cityHistoryVO->setCityId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "init":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $cityHistoryVO->setInitDate(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "end":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $cityHistoryVO->setEndDate(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "userId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $cityHistoryVO->setUserId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $createCityHistories[] = $cityHistoryVO;

            }

        } while ($parser->read());

        //var_dump($createUsers);


        if (count($createCityHistories) >= 1)
            foreach((array)$createCityHistories as $cityHistory)
            {
                if (UsersFacade::CreateCityHistory($cityHistory) == -1)
                {
                    $string = "<return service='createCityHistories'><error id='1'>There was some error while creating the city history entries</error></return>";
                    break;
                }
            }



        if (!$string)
        {

            $string = "<return service='createCityHistories'><ok>Operation Success!</ok><cityHistories>";

            foreach((array) $createCityHistories as $cityHistory)
            {
                $string = $string . "<cityHistory><id>{$cityHistory->getId()}</id><cityId>{$cityHistory->getCityId()}</cityId><init format='Y-m-d'>{$cityHistory->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$cityHistory->getEndDate()->format('Y-m-d')}</end></cityHistory>";
            }

            $string = $string . "</cityHistories></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
