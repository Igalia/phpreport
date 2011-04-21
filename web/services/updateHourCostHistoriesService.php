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

/** updateHourCostHistories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/HourCostHistoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><hourCostHistories><hourCostHistory><id>71</id><userId>81</userId><hourCost>2</hourCost><init format="Y-m-d">2008-06-01</init><end format="Y-m-d">2008-12-01</end></hourCostHistory><hourCostHistory><id>72</id><userId>81</userId><hourCost>1</hourCost><init format="Y-m-d">2008-01-01</init><end format="Y-m-d">2008-06-01</end></hourCostHistory></hourCostHistories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'hourCostHistories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='updateHourCostHistories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateHourCostHistories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "hourCostHistory")
            {

                $hourCostHistoryVO = new HourCostHistoryVO();

                $userAssignGroups = array();

                $parser->read();

                while ($parser->name != "hourCostHistory") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $hourCostHistoryVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "hourCost":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $hourCostHistoryVO->setHourCost($parser->value);
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
                                    $hourCostHistoryVO->setInitDate(date_create($date));
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
                                    $hourCostHistoryVO->setEndDate(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "userId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $hourCostHistoryVO->setUserId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $updateHourCostHistories[] = $hourCostHistoryVO;

            }

        } while ($parser->read());

        //var_dump($updateUsers);


        if (count($updateHourCostHistories) >= 1)
            foreach((array)$updateHourCostHistories as $hourCostHistory)
            {
                if (UsersFacade::UpdateHourCostHistory($hourCostHistory) == -1)
                {
                    $string = "<return service='updateHourCostHistories'><error id='1'>There was some error while updating the hour cost history entries</error></return>";
                    break;
                }
            }



        if (!$string)
        {

            $string = "<return service='updateHourCostHistories'><ok>Operation Success!</ok><hourCostHistories>";

            foreach((array) $updateHourCostHistories as $hourCostHistory)
            {
                $string = $string . "<hourCostHistory><id>{$hourCostHistory->getId()}</id><hourCost>{$hourCostHistory->getHourCost()}</hourCost><init format='Y-m-d'>{$hourCostHistory->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$hourCostHistory->getEndDate()->format('Y-m-d')}</end></hourCostHistory>";
            }

            $string = $string . "</hourCostHistories></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
