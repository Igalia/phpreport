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
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');

$parser = new XMLReader();

$request = trim(file_get_contents('php://input'));

/*$request = '<?xml version="1.0" encoding="UTF-8"?><templates><template><customerId></customerId><projectId>1</projectId><ttype>community</ttype><story>wofff</story><taskStoryId></taskStoryId><telework>true</telework><onsite></onsite><text>wofff</text></template><template><customerId></customerId><projectId>1</projectId><ttype></ttype><story>asdf</story><taskStoryId></taskStoryId><telework>false</telework><onsite>true</onsite><text>woaowa</text></template><template><customerId></customerId><projectId>1</projectId><ttype></ttype><story>waowao</story><taskStoryId></taskStoryId><telework>true</telework><onsite></onsite><text>asdf</text></template></templates>';
*/
$parser->XML($request);

do {

    $parser->read();

    if ($parser->name == 'templates')
    {

        $sid = $parser->getAttribute("sid");

        $parser->read();

    }

    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    $user = LoginManager::isLogged($sid);

    if (!$user)
    {
        $string = "<return service='createTemplates'><success>false</success><error id='2'>You must be logged in</error></return>";
        break;
    }

    if (!LoginManager::isAllowed($sid))
    {
        $string = "<return service='createTemplates'><success>false</success><error id='3'>Forbidden service for this User</error></return>";
        break;
    }

    do {
        if ($parser->name == "template") {

            $template = array();
            $template["telework"] = false;
            $template["onsite"] = false;
            $template["userId"] = $user->getId();

            $parser->read();

            while ($parser->name != "template") {

                switch ($parser->name) {
                    case "story":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["story"] = unescape_string($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "telework":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $template["telework"] = true;
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "onsite":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $template["onsite"] = true;
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "ttype":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["ttype"] = unescape_string($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "text":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["text"] = unescape_string($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "name":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["name"] = unescape_string($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "taskStoryId":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["taskStoryId"] = $parser->value;
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "projectId":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["projectId"] = $parser->value;
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "customerId":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $template["customerId"] = $parser->value;
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    default:
                        $parser->next();
                        break;
                }
            }
            $createTemplates[] = $template;
        }
    } while ($parser->read());

    if (count($createTemplates) >= 1) {
        $parameters[] = ConfigurationParametersManager::getParameter('DB_HOST');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_PORT');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_USER');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_NAME');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_PASSWORD');

        $connectionString = "host=$parameters[0] port=$parameters[1] user=$parameters[2] dbname=$parameters[3] password=$parameters[4]";
        $connect = pg_connect($connectionString);
        pg_set_error_verbosity($connect, PGSQL_ERRORS_VERBOSE);

        foreach ($createTemplates as $key => $template) {
            $sql = "INSERT INTO template (name, story, telework, onsite, text, ttype, usrid, projectid, customerid, task_storyid) VALUES(" .
                DBPostgres::checkStringNull($template["name"]) . ", " .
                DBPostgres::checkStringNull($template["story"]) . ", " .
                DBPostgres::boolToString($template["telework"]) . ", " .
                DBPostgres::boolToString($template["onsite"]) . ", " .
                DBPostgres::checkStringNull($template["text"]) . ", " .
                DBPostgres::checkStringNull($template["ttype"]) . ", " .
                DBPostgres::checkNull($template["userId"]) . ", " .
                DBPostgres::checkNull($template["projectId"]) . ", " .
                DBPostgres::checkNull($template["customerId"]). ", " .
                DBPostgres::checkNull($template["taskStoryId"]) .")";

            $res = pg_query($connect, $sql);
            if ($res == NULL)
                $string = "<return service='createTemplates'><success>false</success><error id='1'>There was some error while creating the tasks</error></return>";

            $createTemplates[$key]["id"] = DBPostgres::getId($connect, "template_id_seq");
        }
        if (!$string)
        {
            $string = "<return service='createTemplates'><success>true</success><ok>Operation Success!</ok><templates>";
            foreach ($createTemplates as $template) {
                $string .= "<template><id>{$template['id']}</id>";
                $string .= "<name>{$template["name"]}</name>";
                $string .= "<story>{$template["story"]}</story>";
                $string .= "<telework>{$template["telework"]}</telework>";
                $string .= "<onsite>{$template["onsite"]}</onsite>";
                $string .= "<text>{$template["text"]}</text>";
                $string .= "<ttype>{$template["ttype"]}</ttype>";
                $string .= "<userId>{$template["userId"]}</userId>";
                $string .= "<projectId>{$template["projectId"]}</projectId>";
                $string .= "<customerId>{$template["customerId"]}</customerId>";
                $string .= "<taskStoryId>{$template["taskStoryId"]}</taskStoryId></template>";
            }
            $string .= "</templates></return>";
            error_log($string);
        }
    }

} while (false);


// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
