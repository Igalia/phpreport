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

/** deleteUsers web service.
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

    /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><users><user><id>97</id></user></users>';*/

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
            $string = "<return service='deleteUsers'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteUsers'><error id='3'>Forbidden service for this User</error></return>";
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

                        default:    $parser->next();
                                break;

                    }

                }

                $deleteUsers[] = $userVO;

            }

        } while ($parser->read());

        try {
            $groups = UsersFacade::GetAllUserGroups();

            foreach ((array)$groups as $group)
                foreach((array)$deleteUsers as $user)
                    UsersFacade::DeassignUserFromUserGroup($user->getId(), $group->getId());
        }
        catch (LDAPInvalidOperationException $e) {
            // the LDAP backend is enabled and UserGroup operations are forbidden
            // we can ignore this error
        }

        $successMessage = "";
        if (count($deleteUsers) >= 1)
            foreach((array)$deleteUsers as $user)
            {
                $result = UsersFacade::DeleteUser($user);
                if ($result->getIsSuccessful()) {
                    $successMessage .= $result->getMessage() . "\n";
                }
                else {
                    http_response_code($result->getResponseCode());
                    $string = "<return service='updateUsers'><error id='" . $result->getErrorNumber() . "'>" .
                            $result->getMessage() . "</error></return>";
                    break;
                }
            }



        if (!$string)
            $string = "<return service='deleteUsers'><ok>" . $successMessage . "</ok></return>";

    } while (false);


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
