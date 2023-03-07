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

/** deleteTasks web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

     /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><tasks><task><date>2009-12-01</date><id>124303</id></task></tasks>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'tasks')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        // We check authentication and authorization
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='deleteTasks'><success>false</success><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteTasks'><success>false</success><error id='3'>Forbidden service for this User</error></return>";
            break;
        }


        do {

            if ($parser->name == "task")
            {

                $taskVO = new TaskVO();

                $taskVO->setTelework(false);

                $parser->read();

                while ($parser->name != "task") {

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "date":
                            $dateFormat = $parser->getAttribute("format");
                            if (is_null($dateFormat)) {
                                $dateFormat = "Y-m-d";
                            }
                            $parser->read();
                            if ($parser->hasValue) {
                                $dateString = $parser->value;
                                $date = date_create_from_format(
                                        $dateFormat, $dateString);
                                $taskVO->setDate($date);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        default:    $parser->next();
                                break;

                    }


                }

                $taskVO->setUserId($user->getId());

                $deleteTasks[] = $taskVO;

            }

        } while ($parser->read());

        $operationResults = TasksFacade::DeleteReports($deleteTasks);
        $errors = array_filter($operationResults, function ($item) {
            return (!$item->getIsSuccessful());
        });
        if ($errors) {
            // if multiple failures, just return the code of the first one
            http_response_code($errors[0]->getResponseCode());
            $string = "<return service='deleteTasks'><errors>";
            foreach((array) $errors as $result){
                if (!$result->getIsSuccessful())
                    $string .= "<error id='" . $result->getErrorNumber() . "'>" . $result->getMessage() . "</error>";
            }
            $string .= "</errors></return>";
        }

        if (!isset($string))
            $string = "<return service='deleteTasks'><success>true</success><ok>Operation Success!</ok></return>";


    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
