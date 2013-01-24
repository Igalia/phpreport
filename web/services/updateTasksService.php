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

/** updateTasks web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/DirtyTaskVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

        /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><tasks><task><date>2009-11-30</date><initTime>00:15</initTime><story>jjjjjjj</story><id>124283</id></task></tasks>';
         */

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'tasks')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='updateTasks'><success>false</success><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateTasks'><success>false</success><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "task")
            {

                $taskVO = new DirtyTaskVO();

                $parser->read();

                while ($parser->name != "task") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "date":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $taskVO->setDate(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setDate(NULL);
                                }
                                break;

                        case "initTime": $initTimeFormat = $parser->getAttribute("format");
                                if (is_null($initTimeFormat))
                                    $initTimeFormat = "H:i";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $initTime = $parser->value;
                                    $initTimeParse = date_parse_from_format($initTimeFormat, $initTime);
                                    $initTime = $initTimeParse['hour']*60 + $initTimeParse['minute'];
                                    $taskVO->setInit($initTime);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setInit(NULL);
                                }
                                break;

                        case "endTime": $endTimeFormat = $parser->getAttribute("format");
                                if (is_null($endTimeFormat))
                                    $endTimeFormat = "H:i";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $endTime = $parser->value;
                                    $endTimeParse = date_parse_from_format($endTimeFormat, $endTime);
                                    if (($endTimeParse['hour']==0) && ($endTimeParse['minute']==0)) $endTimeParse['hour'] = 24;
                                    $endTime = $endTimeParse['hour']*60 + $endTimeParse['minute'];
                                    $taskVO->setEnd($endTime);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setEnd(NULL);
                                }
                                break;

                        case "story":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setStory(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setStory(NULL);
                                }
                                break;

                        case "telework":$parser->read();
                                if ($parser->hasValue)
                                {
                                    if (strtolower($parser->value) == "true")
                                        $taskVO->setTelework(true);
                                    else
                                        $taskVO->setTelework(false);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setTelework(NULL);
                                }
                                break;

                        case "onsite":$parser->read();
                                if ($parser->hasValue)
                                {
                                    if (strtolower($parser->value) == "true")
                                        $taskVO->setOnsite(true);
                                    else
                                        $taskVO->setOnsite(false);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setOnsite(NULL);
                                }
                                break;

                        case "ttype":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setTtype(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setTtype(NULL);
                                }
                                break;

                        case "text":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setText(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setText(NULL);
                                }
                                break;

                        case "phase":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setPhase(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setPhase(NULL);
                                }
                                break;

                        case "taskStoryId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setTaskStoryId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setTaskStoryId(NULL);
                                }
                                break;

                        case "projectId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setProjectId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setProjectId(NULL);
                                }
                                break;

                        case "customerId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setCustomerId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                else {
                                    $taskVO->setCustomerId(NULL);
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }


                }

                $taskVO->setUserId($user->getId());

                $updateTasks[] = $taskVO;

            }

        } while ($parser->read());


        if (count($updateTasks) >= 1)
            if (TasksFacade::PartialUpdateReports($updateTasks) == -1)
                $string = "<return service='updateTasks'><success>false</success><error id='1'>There was some error while updating the tasks</error></return>";


        if (!isset($string))
            $string = "<return service='updateTasks'><success>true</success><ok>Operation Success!</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
