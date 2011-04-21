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

/** updateTaskSections web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><taskSections><taskSection><id>3</id><risk>2</risk><estHours>50</estHours><name>Gallina</name><userId>81</userId><sectionId>2</sectionId></taskSection><taskSection><id>4</id><risk>0</risk><estHours>30</estHours><name>This is Igalia!!</name><userId>75</userId><taskSectionId/><sectionId>2</sectionId></taskSection></taskSections>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'taskSections')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='updateTaskSections'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateTaskSections'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "taskSection")
            {

                $taskSectionVO = new TaskSectionVO();

                $parser->read();

                while ($parser->name != "taskSection") {

                    switch ($parser->name) {

                        case "id":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "name":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setName(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "risk":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setRisk($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "estHours":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setEstHours($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "userId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setUserId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "sectionId":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $taskSectionVO->setSectionId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }


                }

                $updateTaskSections[] = $taskSectionVO;

            }

        } while ($parser->read());


        if (count($updateTaskSections) >= 1)
            foreach((array)$updateTaskSections as $taskSection)
                if (CoordinationFacade::UpdateTaskSection($taskSection) == -1)
                {
                    $string = "<return service='updateTaskSections'><error id='1'>There was some error while updating the task sections</error></return>";
                    break;
                }


        if (!$string)
        {

            $string = "<return service='updateTaskSections'><ok>Operation Success!</ok><taskSections>";

            foreach((array) $updateTaskSections as $updateTaskSection)
            {

                $taskSection = CoordinationFacade::GetCustomTaskSection($updateTaskSection->getId());

                $string = $string . "<taskSection><id>{$taskSection->getId()}</id><risk>{$taskSection->getRisk()}</risk><name>{$taskSection->getName()}</name><estHours>{$taskSection->getEstHours()}</estHours><spent>{$taskSection->getSpent()}</spent><toDo>{$taskSection->getToDo()}</toDo><developer>";

                $developer = $taskSection->getDeveloper();

                if ($developer)
                    $string = $string . "<id>{$developer->getId()}</id><login>{$developer->getLogin()}</login>";

                $string = $string . "</developer><reviewer>";

                $reviewer = $taskSection->getReviewer();

                if ($reviewer)
                    $string = $string . "<id>{$reviewer->getId()}</id><login>{$reviewer->getLogin()}</login>";

                $string = $string . "</reviewer>";

                $string = $string . "</taskSection>";

            }

            $string = $string . "</taskSections></return>";

        }


    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
