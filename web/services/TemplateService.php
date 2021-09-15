<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

namespace Phpreport\Web\services;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/TemplatesFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/TemplateVO.php');

class TemplateService
{
    private \LoginManager $loginManager;

    public function __construct(
        \LoginManager $loginManager
    ) {
        $this->loginManager = $loginManager;
    }

    public function parseTemplates(\XMLReader $parser, string $userId): array
    {
        $createTemplates = [];
        do {
            if ($parser->name == "template") {
                $templatesVO = new \TemplateVO();

                $templatesVO->setTelework(false);
                $templatesVO->setOnsite(false);
                $templatesVO->setUserId($userId);

                $parser->read();

                while ($parser->name != "template") {

                    switch ($parser->name) {
                        case "story":
                            $parser->read();
                            if ($parser->hasValue) {
                                $templatesVO->setStory(unescape_string($parser->value));
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "telework":
                            $parser->read();
                            if ($parser->hasValue) {
                                if (strtolower($parser->value) == "true")
                                    $templatesVO->setTelework(true);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "onsite":
                            $parser->read();
                            if ($parser->hasValue) {
                                if (strtolower($parser->value) == "true")
                                    $templatesVO->setOnsite(true);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "ttype":
                            $parser->read();
                            if ($parser->hasValue) {
                                $templatesVO->setTtype(unescape_string($parser->value));
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "text":
                            $parser->read();
                            if ($parser->hasValue) {
                                $templatesVO->setText(unescape_string($parser->value));
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "name":
                            $parser->read();
                            if ($parser->hasValue) {
                                $templatesVO->setName(substr(unescape_string($parser->value), 0, 75));
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "taskStoryId":
                            $parser->read();
                            if ($parser->hasValue) {
                                $templatesVO->setTaskStoryId($parser->value);
                                $parser->next();
                                $parser->next();
                            }
                            break;

                        case "projectId":
                            $parser->read();
                            if ($parser->hasValue) {
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
                            if ($parser->hasValue) {
                                $initTimeRaw = $parser->value;
                                $initTimeParse = date_parse_from_format($initTimeFormat, $initTimeRaw);
                                $initTime = $initTimeParse['hour'] * 60 + $initTimeParse['minute'];
                                $templatesVO->setInitTime($initTime);
                                $templatesVO->setInitTimeRaw($initTimeRaw);
                                $parser->next();
                                $parser->next();
                            } else {
                                $templatesVO->setInitTime(NULL);
                            }
                            break;

                        case "endTime":
                            $endTimeFormat = $parser->getAttribute("format");
                            if (is_null($endTimeFormat))
                                $endTimeFormat = "H:i";
                            $parser->read();
                            if ($parser->hasValue) {
                                $endTimeRaw = $parser->value;
                                $endTimeParse = date_parse_from_format($endTimeFormat, $endTimeRaw);
                                if (($endTimeParse['hour'] == 0) && ($endTimeParse['minute'] == 0)) $endTimeParse['hour'] = 24;
                                $endTime = $endTimeParse['hour'] * 60 + $endTimeParse['minute'];
                                $templatesVO->setEndTime($endTime);
                                $templatesVO->setEndTimeRaw($endTimeRaw);
                                $parser->next();
                                $parser->next();
                            } else {
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
        return $createTemplates;
    }

    public function createTemplate(string $request): string
    {
        $parser = new \XMLReader();

        $parser->XML($request);
        $sid = NULL;

        do {

            $parser->read();

            if ($parser->name == 'templates') {

                $sid = $parser->getAttribute("sid");

                $parser->read();
            }

            /* We check authentication and authorization */
            $user = $this->loginManager::isLogged($sid);

            if (!$user) {
                $string = "<return service='createTemplates'><success>false</success><error id='2'>You must be logged in</error></return>";
                break;
            }

            if (!$this->loginManager::isAllowed($sid)) {
                $string = "<return service='createTemplates'><success>false</success><error id='3'>Forbidden service for this User</error></return>";
                break;
            }

            $createTemplates = $this->parseTemplates($parser, $user->getId());

            $string = "";
            if (count($createTemplates) >= 1) {
                if (\TemplatesFacade::CreateTemplates($createTemplates) == -1)
                    $string = "<return service='createTemplates'><success>false</success><error id='1'>There was some error while creating the tasks</error></return>";

                if (!$string) {
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

        // output correctly formatted XML
        return $xml->asXML();
    }
}
