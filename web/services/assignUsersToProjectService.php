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

/** assignUsersToProject web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><users projectId="1"><user><id>65</id></user><user><id>1</id></user></users>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'users')
        {

            $sid = $parser->getAttribute("sid");

            $pid = $parser->getAttribute("projectId");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='assignUsersToProject'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='assignUsersToProject'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            if ($parser->name == "user")
            {

                $parser->read();

                while ($parser->name != "user") {

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $userVO = UsersFacade::GetUser($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $assignUsers[] = $userVO;

            }

        } while ($parser->read());


        if (count($assignUsers) >= 1)
            foreach((array)$assignUsers as $assignUser)
            {
                if (ProjectsFacade::AssignUserToProject($assignUser->getId(), $pid) == -1)
                {
                    $string = "<return service='assignUsersToProject'><error id='1'>There was some error while assigning the users</error></return>";
                    break;
                }
            }



        if (!$string)
        {

            $string = "<return service='assignUsersToProject'><ok>Operation Success!</ok><users>";

            foreach((array) $assignUsers as $assignUser)
            {

                $string = $string . "<user><id>{$assignUser->getId()}</id><login>{$assignUser->getLogin()}</login><userGroups>";

                foreach ((array) $assignUser->getGroups() as $group)
                    $string = $string . "<{$group->getName()}>true</{$group->getName()}>";

                $string = $string . "</userGroups></user>";

                }

                $string = $string . "</users></return>";

        }

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
