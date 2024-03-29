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

define('PHPREPORT_ROOT', __DIR__ . '/../../');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/TemplatesFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');

$parser = new XMLReader();

$request = trim(file_get_contents('php://input'));

 /*$request = '<?xml version="1.0" encoding="ISO-8859-15"?><tasks><task><date>2009-12-01</date><id>124303</id></task></tasks>';*/

$parser->XML($request);
$sid = NULL;

do {

    $parser->read();

    if ($parser->name == 'templates')
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

    $deleteTemplates = array();

    do {
        if ($parser->name == "template") {

            $templatesVO = new TemplateVO();

            $parser->read();

            while ($parser->name != "template") {

                switch ($parser->name) {

                    case "id":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    default:
                        $parser->next();
                        break;
                }
            }

            $templatesVO->setUserId($user->getId());

            $deleteTemplates[] = $templatesVO;

        }

    } while ($parser->read());

    $string = "";
    if (count($deleteTemplates) >= 1) {
        if (TemplatesFacade::DeleteTemplates($deleteTemplates) == -1)
            $string = "<return service='deleteTemplates'><success>false</success><error id='1'>There was some error while deleting the tasks</error></return>";

        if (!$string)
        {
            $string = "<return service='deleteTemplates'><success>true</success><ok>Operation Success!</ok><templates></templates></return>";
        }
    }

} while (false);


// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
