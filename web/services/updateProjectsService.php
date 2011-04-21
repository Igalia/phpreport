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

/** updateProjects web service.
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

    //$request = "<projects sid='902b2f21b1bfbd5b389310b2de703ac7'><project><id>244</id><activation>false</activation><init format='Y-m-d'>2009-2-10</init><end format='Y-m-d'>2009-11-15</end><invoice>450</invoice><estHours>23</estHours><areaId>1</areaId><description>Lol Project</description><type>Chorritest</type><movHours>5</movHours><schedType>Chorro schedule</schedType></project></projects>";

    $parser->XML($request);

    while ($parser->read()) {

    if ($parser->name == "projects")
    {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        $sid = $parser->getAttribute("sid");

        if (!LoginManager::isLogged($sid))
        {
            $string = "<return service='updateProjects'><error id='2'>You must be logged in</error></return>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<return service='updateProjects'><error id='3'>Forbidden service for this User</error></return>";
            break;
        }

    }
    elseif ($parser->name == "project")
    {

        $projectVO = new ProjectVO();

        $update = array();

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

                case "activation":$parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $projectVO->setActivation(true);
                            else
                                $projectVO->setActivation(false);
                            $parser->next();
                            $parser->next();
                        }
                        $update[activation] = true;
                        break;

                case "init":    $dateFormat = $parser->getAttribute("format");
                        if (is_null($dateFormat))
                            $dateFormat = "Y-m-d";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $date = $parser->value;
                            $dateParse = date_parse_from_format($dateFormat, $date);
                            $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                            $projectVO->setInit(date_create($date));
                            $parser->next();
                            $parser->next();
                        }
                        $update[init] = true;
                        break;

                case "end":    $dateFormat = $parser->getAttribute("format");
                        if (is_null($dateFormat))
                            $dateFormat = "Y-m-d";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $date = $parser->value;
                            $dateParse = date_parse_from_format($dateFormat, $date);
                            $date = "{$dateParse['year']}-{$dateParse['month']}-{$dateParse['day']}";
                            $projectVO->setEnd(date_create($date));
                            $parser->next();
                            $parser->next();
                        }
                        $update[end] = true;
                        break;

                case "invoice":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setInvoice($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        $update[invoice] = true;
                        break;

                case "estHours":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setEstHours($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        $update[estHours] = true;
                        break;

                case "description":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setDescription(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        $update[description] = true;
                        break;

                case "areaId":$parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setAreaId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        $update[areaId] = true;
                        break;

                case "type":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setType(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        $update[type] = true;
                        break;

                case "movedHours":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setMovedHours($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        $update[movHours] = true;
                        break;

                case "schedType":    $parser->read();
                        if ($parser->hasValue)
                        {
                            $projectVO->setSchedType(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        $update[schedType] = true;
                        break;

                default:    $parser->next();
                        break;

            }


        }

        $updateProjects[] = $projectVO;

        $updates[] = $update;

    }

    }


    if (count($updateProjects) >= 1)
        if (ProjectsFacade::PartialUpdateProjects($updateProjects, $updates) == -1)
            $string = "<return service='updateProjects'><error id='1'>There was some error while updating the projects</error></return>";


    if (!$string)
    $string = "<return service='updateProjects'><ok>Operation Success!</ok></return>";


    // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
