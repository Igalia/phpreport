<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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

/** updateAreaHistories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 */

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');

$parser = new XMLReader();

$request = trim(file_get_contents('php://input'));

$parser->XML($request);

do {

    $parser->read();

    if ($parser->name == 'userGoals')
    {

        $sid = $parser->getAttribute("sid");

        $parser->read();

    }

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    $user = LoginManager::isLogged($sid);

    if (!$user)
    {
        $string = "<return service='updateUserGoals'><error id='2'>You must be logged in</error></return>";
        break;
    }

    if (!LoginManager::isAllowed($sid))
    {
        $string = "<return service='updateUserGoals'><error id='3'>Forbidden service for this User</error></return>";
        break;
    }
    $updateUserGoals = array();
    do {

        if ($parser->name == "userGoal")
        {

            $userGoalVO = new UserGoalVO();

            $parser->read();

            while ($parser->name != "userGoal") {


                switch ($parser->name ) {

                    case "id":$parser->read();
                        if ($parser->hasValue)
                        {
                            $userGoalVO->setId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "extraTime":$parser->read();
                        if ($parser->hasValue)
                        {
                            $userGoalVO->setExtraHours($parser->value);
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
                            $userGoalVO->setInitDate(date_create($date));
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
                            $userGoalVO->setEndDate(date_create($date));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "userId":$parser->read();
                        if ($parser->hasValue)
                        {
                            $userGoalVO->setUserId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    default:    $parser->next();
                        break;

                }

            }

            $updateUserGoals[] = $userGoalVO;
        }

    } while ($parser->read());

    if (count($updateUserGoals) >= 1) {
        foreach ( (array) $updateUserGoals as $userGoal ) {
            if ( UsersFacade::UpdateUserGoal( $userGoal ) == -1 ) {
                $string = "<return service='updateUserGoals'><error id='1'>There was some error while updating the user goal entries</error></return>";
                break;
            }
        }
    }

    if (!isset($string))
    {

        $string = "<return service='updateUserGoals'><ok>Operation Success!</ok><userGoals>";

        foreach((array) $updateUserGoals as $updatedUserGoal)
        {
            $string = $string . "<userGoal><id>{$updatedUserGoal->getId()}</id><extraTime>{$updatedUserGoal->getExtraHours()}</extraTime><init format='Y-m-d'>{$updatedUserGoal->getInitDate()->format('Y-m-d')}</init><end format='Y-m-d'>{$updatedUserGoal->getEndDate()->format('Y-m-d')}</end></userGoal>";
        }

        $string = $string . "</userGoals></return>";

    }

} while (false);


// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
