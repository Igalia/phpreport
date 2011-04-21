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

/** deleteProjects web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
    include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

    $parser = new XMLReader();

    $request = trim(file_get_contents('php://input'));

    //$request = "<projects sid='902b2f21b1bfbd5b389310b2de703ac7'><project><id>244</id></project></projects>";

    $parser->XML($request);

    $newProject = TRUE;

    while ($parser->read()) {

    if ($parser->name == "projects")
    {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $sid = $parser->getAttribute("sid");

        if (!LoginManager::isLogged($sid))
        {
            $string = "<return service='deleteProjects'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='deleteProjects'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

    }
    elseif ($parser->name == "project")
    {

        $projectVO = new ProjectVO();

        $parser->read();

        while ($parser->name != "project") {

            //print ($parser->name . "\n");

            switch ($parser->name ) {

                case "id":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                default:    $parser->next();
                        break;

            }


        }

        $deleteProjects[] = $projectVO;

    }

    }


    if (count($deleteProjects) >= 1)
        if (ProjectsFacade::DeleteProjects($deleteProjects) == -1)
            $string = "<return service='deleteProjects'><error id='1'>There was some error while deleting the projects</error></return>";

    if (!$string)
        $string = "<return service='deleteProjects'><ok>Operation Success!</ok></return>";


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
