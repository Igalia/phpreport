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

/** createTaskStories web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><taskStories><taskStory><initDate>2009-10-01</initDate><end>2009-10-25</end><estEnd>2009-10-29</estEnd><risk>2</risk><toDo>10.5</toDo><estHours>50</estHours><name>Pollito</name><userId>81</userId><taskSectionId/><storyId>1</storyId></taskStory><taskStory><initDate>2008-10-01</initDate><endDate>2008-10-25</endDate><estEndDate>2008-10-29</estEndDate><risk>0</risk><toDo>5</toDo><estHours>30</estHours><name>This is Igalia!!</name><userId>75</userId><taskSectionId/><storyId>1</storyId></taskStory></taskStories>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'taskStories')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='createTaskStories'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='createTaskStories'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "taskStory")
            {

                $taskStoryVO = new TaskStoryVO();

                $parser->read();

                while ($parser->name != "taskStory") {

                    switch ($parser->name) {

                        case "initDate":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $taskStoryVO->setInit(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "endDate":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $taskStoryVO->setEnd(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "estEndDate":    $dateFormat = $parser->getAttribute("format");
                                if (is_null($dateFormat))
                                    $dateFormat = "Y-m-d";
                                $parser->read();
                                if ($parser->hasValue)
                                {
                                    $date = $parser->value;
                                    $dateParse = date_parse_from_format($dateFormat, $date);
                                    $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                                    $taskStoryVO->setEstEnd(date_create($date));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "name":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setName(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "risk":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setRisk($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "toDo":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setToDo($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "estHours":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setEstHours($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "taskSectionId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setTaskSectionId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "userId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setUserId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "storyId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskStoryVO->setStoryId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }


                }

                $createTaskStories[] = $taskStoryVO;

            }

        } while ($parser->read());


        if (count($createTaskStories) >= 1)
            foreach((array)$createTaskStories as $taskStory)
                if (CoordinationFacade::CreateTaskStory($taskStory) == -1)
                {
                    $string = "<return service='createTaskStories'><error id='1'>There was some error while creating the task stories</error></return>";
                    break;
                }


        if (!$string)
        {

            $string = "<return service='createTaskStories'><ok>Operation Success!</ok><taskStories>";

            foreach((array) $createTaskStories as $createTaskStory)
            {

                $taskStory = CoordinationFacade::GetCustomTaskStory($createTaskStory->getId());

                $string = $string . "<taskStory><id>{$taskStory->getId()}</id><risk>{$taskStory->getRisk()}</risk><name>{$taskStory->getName()}</name><estHours>{$taskStory->getEstHours()}</estHours><spent>{$taskStory->getSpent()}</spent><toDo>{$taskStory->getToDo()}</toDo><developer>";

                $developer = $taskStory->getDeveloper();

                if ($developer)
                    $string = $string . "<id>{$developer->getId()}</id><login>{$developer->getLogin()}</login>";

                $string = $string . "</developer><reviewer>";

                $reviewer = $taskStory->getReviewer();

                if ($reviewer)
                    $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

                $string = $string . "</reviewer>";

                if (!is_null($taskStory->getInit()))
                    $string = $string . "<initDate format='Y-m-d'>{$taskStory->getInit()->format("Y-m-d")}</initDate>";
                else    $string = $string . "<initDate/>";

                if (!is_null($taskStory->getEnd()))
                    $string = $string . "<endDate format='Y-m-d'>{$taskStory->getEnd()->format("Y-m-d")}</endDate>";
                else    $string = $string . "<endDate/>";

                if (!is_null($taskStory->getEstEnd()))
                    $string = $string . "<estEndDate format='Y-m-d'>{$taskStory->getEstEnd()->format("Y-m-d")}</estEndDate>";
                else    $string = $string . "<estEndDate/>";

                if (!is_null($taskStory->getTaskSection()))
                    $string = $string . "<taskSection><id>{$taskStory->getTaskSection()->getId()}<id/><name>{$taskStory->getTaskSection()->getName()}</name></taskSection>";
                else    $string = $string . "<taskSection/>";

                $string = $string . "</taskStory>";

            }

            $string = $string . "</taskStories></return>";

        }


    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
