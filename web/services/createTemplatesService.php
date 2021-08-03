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

    $createTemplates = array();

    do {
        if ($parser->name == "template") {
            $templatesVO = new TemplateVO();

            $templatesVO->setTelework(false);
            $templatesVO->setOnsite(false);
            $templatesVO->setUserId($user->getId());

            $parser->read();

            while ($parser->name != "template") {

                switch ($parser->name) {
                    case "story":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setStory(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "telework":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $templatesVO->setTelework(true);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "onsite":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            if (strtolower($parser->value) == "true")
                                $templatesVO->setOnsite(true);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "ttype":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setTtype(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "text":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setText(unescape_string($parser->value));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "name":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setName(substr(unescape_string($parser->value), 0, 75));
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "taskStoryId":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setTaskStoryId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "projectId":
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $templatesVO->setProjectId($parser->value);
                            $parser->next();
                            $parser->next();
                        }
                        break;

                    case "initTime":
                        $initTimeFormat = $parser->getAttribute("format");
                        if (is_null($initTimeFormat))
                            $initTimeFormat = "H:i";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $initTimeRaw = $parser->value;
                            $initTimeParse = date_parse_from_format($initTimeFormat, $initTimeRaw);
                            $initTime = $initTimeParse['hour']*60 + $initTimeParse['minute'];
                            $templatesVO->setInitTime($initTime);
                            $templatesVO->setInitTimeRaw($initTimeRaw);
                            $parser->next();
                            $parser->next();
                        }
                        else {
                            $templatesVO->setInitTime(NULL);
                        }
                        break;

                    case "endTime":
                        $endTimeFormat = $parser->getAttribute("format");
                        if (is_null($endTimeFormat))
                            $endTimeFormat = "H:i";
                        $parser->read();
                        if ($parser->hasValue)
                        {
                            $endTimeRaw = $parser->value;
                            $endTimeParse = date_parse_from_format($endTimeFormat, $endTimeRaw);
                            if (($endTimeParse['hour']==0) && ($endTimeParse['minute']==0)) $endTimeParse['hour'] = 24;
                            $endTime = $endTimeParse['hour']*60 + $endTimeParse['minute'];
                            $templatesVO->setEndTime($endTime);
                            $templatesVO->setEndTimeRaw($endTimeRaw);
                            $parser->next();
                            $parser->next();
                        }
                        else {
                            $templatesVO->setEndTime(NULL);
                        }
                        break;
                    default:
                        $parser->next();
                        break;
                }
            }
            $createTemplates[] = $templatesVO;
        }
    } while ($parser->read());

    if (count($createTemplates) >= 1) {
        $string = "";
        if (TemplatesFacade::CreateTemplates($createTemplates) == -1)
            $string = "<return service='createTemplates'><success>false</success><error id='1'>There was some error while creating the tasks</error></return>";

        if (!$string)
        {
            $string = "<return service='createTemplates'><success>true</success><ok>Operation Success!</ok><templates>";
            foreach ($createTemplates as $template) {
                $string .= $template->toXml();
            }
            $string .= "</templates></return>";
        }
    }

} while (false);


// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
