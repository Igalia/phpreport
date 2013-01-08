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

/** createTasks web service.
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

        /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><tasks><task><date>2009-10-29</date><initTime>00:00</initTime><endTime>03:00</endTime><customerId>10</customerId><projectId>196</projectId><ttype>hjgjhg</ttype><story>jhgjhgjk</story><text>uyefsdgfdghfdhgf</text><telework>true</telework></task><task><date>2009-10-28</date><initTime>03:30</initTime><endTime>10:45</endTime><customerId>10</customerId><projectId>196</projectId><ttype>hjgjhg</ttype><story>jhgjhgjk</story><text>uyefsdgfdghfdhgf</text><telework>true</telework></task></tasks>';*/

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
            $string = "<return service='createTasks'><success>false</success><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createTasks'><success>false</success><error id='3'>Forbidden service for this User</error></return>";
            break;
                }

        do {

            if ($parser->name == "task")
            {

                $taskVO = new TaskVO();

                $taskVO->setTelework(false);

                $parser->read();

                while ($parser->name != "task") {

                    switch ($parser->name) {

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
                                break;

                        case "endTime": $endTimeFormat = $parser->getAttribute("format");
                                if (is_null($endTimeFormat))
                                    $endTimeFormat = "H:i";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $endTime = $parser->value;
                                    $endTimeParse = date_parse_from_format($endTimeFormat, $endTime);
                                    if (($endTimeParse['hour']==0)&&($endTimeParse['minute']==0)) $endTimeParse['hour']=24;
                                    $endTime = $endTimeParse['hour']*60 + $endTimeParse['minute'];
                                    $taskVO->setEnd($endTime);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "story":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setStory(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "telework":$parser->read();
                                if ($parser->hasValue)
                                {
                                    if (strtolower($parser->value) == "true")
                                        $taskVO->setTelework(true);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "onsite":$parser->read();
                                if ($parser->hasValue)
                                {
                                    if (strtolower($parser->value) == "true")
                                        $taskVO->setOnsite(true);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "ttype":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setTtype(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "text":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setText(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "phase":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setPhase(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "taskStoryId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setTaskStoryId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "projectId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setProjectId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "customerId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskVO->setCustomerId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }


                }

                $taskVO->setUserId($user->getId());

                $createTasks[] = $taskVO;

            }

        } while ($parser->read());


        if (count($createTasks) >= 1)
            if (TasksFacade::CreateReports($createTasks) == -1)
                $string = "<return service='createTasks'><success>false</success><error id='1'>There was some error while creating the tasks</error></return>";


        if (!$string)
        {

            $string = "<return service='createTasks'><success>true</success><ok>Operation Success!</ok><tasks>";

                foreach((array) $createTasks as $task)
            {

            $string = $string . "<task><id>{$task->getId()}</id><date format='$dateFormat'>{$task->getDate()->format($dateFormat)}</date><initTime>" . str_pad(floor($task->getInit()/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($task->getInit()%60, 2, "0", STR_PAD_LEFT)  . "</initTime><endTime>" . str_pad(floor($task->getEnd()/60), 2, "0", STR_PAD_LEFT) . ":" . str_pad($task->getEnd()%60, 2, "0", STR_PAD_LEFT)  . "</endTime><story>" . escape_string($task->getStory()) . "</story><telework>{$task->getTelework()}</telework><onsite>{$task->getOnsite()}</onsite><ttype>" . escape_string($task->getTtype()) . "</ttype><text>" . escape_string($task->getText()) . "</text><phase>" . escape_string($task->getPhase()) . "</phase><userId>{$task->getUserId()}</userId><projectId>{$task->getProjectId()}</projectId><customerId>{$task->getCustomerId()}</customerId><taskStoryId>{$task->getTaskStoryId()}</taskStoryId></task>";

            }

            $string = $string . "</tasks></return>";

                }


    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
