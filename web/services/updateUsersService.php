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

/** updateUsers web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
    include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><users><user><id>65</id><login>berto</login><userGroups><admin>false</admin><staff>true</staff></userGroups></user></users>';*/

    $parser->XML($request);

    do {

        $parser->read();

        if ($parser->name == 'users')
        {

            $sid = $parser->getAttribute("sid");

            $parser->read();

        }

        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $user = LoginManager::isLogged($sid);

        if (!$user)
        {
            $string = "<return service='updateUsers'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateUsers'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

        do {

            //print ($parser->name . "\n");

            if ($parser->name == "user")
            {

                $userVO = new UserVO();

                $userAssignGroups = array();

                $userDeassignGroups = array();

                $parser->read();

                while ($parser->name != "user") {

                    //print ($parser->name . "\n");

                    switch ($parser->name ) {

                        case "id":    $parser->read();
                                if ($parser->hasValue)
                                {
                                    $userVO->setId($parser->value);
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "login":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $userVO->setLogin(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "password":$parser->read();
                                if ($parser->hasValue)
                                {
                                    $userVO->setPassword(unescape_string($parser->value));
                                    $parser->next();
                                    $parser->next();
                                }
                                break;

                        case "userGroups":

                                $parser->read();

                                while($parser->name != "userGroups")
                                {

                                    //print ($parser->name . "\n");

                                    $userGroupName = $parser->name;

                                    $parser->read();

                                    if ($parser->hasValue)
                                    {
                                        $userGroup = new UserGroupVO();

                                        $userGroup->setName($userGroupName);

                                        if (strtolower($parser->value) == "true")
                                        {
                                            $userAssignGroups[] = $userGroup;

                                        } else {

                                            $userDeassignGroups[] = $userGroup;

                                        }

                                        $parser->next();
                                        $parser->next();

                                    }


                                }

                                $parser->next();
                                break;

                        default:    $parser->next();
                                break;

                    }

                }

                $userVO->setGroups($userAssignGroups);

                $deassignGroups[$userVO->getId()] = $userDeassignGroups;

                $updateUsers[] = $userVO;

            }

        } while ($parser->read());

        //var_dump($updateUsers);


        if (count($updateUsers) >= 1)
            foreach((array)$updateUsers as $updateUser)
            {
                if (UsersFacade::UpdateUser($updateUser) == -1)
                {
                    $string = "<return service='updateUsers'><error id='1'>There was some error while updating the users</error></return>";
                    break;
                }

                foreach((array) $updateUser->getGroups() as $group)
                {

                    $group = UsersFacade::GetUserGroupByName($group->getName());

                    if (UsersFacade::AssignUserToUserGroup($updateUser->getId(), $group->getId()) == -1)
                    {
                        $string = "<return service='updateUsers'><error id='1'>There was some error while updating the user groups new assignements</error></return>";
                        break;
                    }

                }
            }

            foreach((array) $deassignGroups as $userId=>$groups)
                foreach((array) $groups as $group)
                {

                    $group = UsersFacade::GetUserGroupByName($group->getName());

                    if (UsersFacade::DeassignUserFromUserGroup($userId, $group->getId()) == -1)
                    {
                        $string = "<return service='updateUsers'><error id='1'>There was some error while updating the user groups new deassignements</error></return>";
                        break;
                    }

                }



        if (!$string)
        {

            $string = "<return service='updateUsers'><ok>Operation Success!</ok><users>";

            foreach((array) $updateUsers as $updateUser)
            {

                $string = $string . "<user><id>{$updateUser->getId()}</id><login>{$updateUser->getLogin()}</login><userGroups>";

                foreach ((array) $updateUser->getGroups() as $group)
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
