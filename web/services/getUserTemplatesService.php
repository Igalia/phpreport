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

$sid = $_GET['sid'] ?? NULL;

do {
    /* We check authentication and authorization */
    require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

    $user = LoginManager::isLogged($sid);

    if (!$user)
    {
        $string = "<templates><error id='2'>You must be logged in</error></templates>";
        break;
    }

    if (!LoginManager::isAllowed($sid))
    {
        $string = "<templates><error id='3'>Forbidden service for this User</error></templates>";
        break;
    }

    $templates = TemplatesFacade::GetUserTemplates($user->getId());

    $string = "<templates login='" . $user->getLogin() . "'>";

    if(count($templates) > 0) {
        foreach ($templates as $templateVO) {
            $string .= "<template><id>{$templateVO->getId()}</id>";
            $string .= "<story>" . escape_string( $templateVO->getStory() ) . "</story>";
            $string .= "<ttype>" . escape_string( $templateVO->getTtype() ) . "</ttype>";
            $string .= "<name>" . escape_string( $templateVO->getName() ) . "</name>";
            $string .= "<text>" . escape_string( $templateVO->getText() ) . "</text>";
            $string .= "<userId>{$templateVO->getUserId()}</userId>";
            $string .= "<projectId>{$templateVO->getProjectId()}</projectId>";
            $string .= "<taskStoryId>{$templateVO->getTaskStoryId()}</taskStoryId>";

            $string .= "<telework>";
            if ( strtolower( $templateVO->isTelework() ) == 1 ) {
                $string .= "true";
            } else {
                $string .= "false";
            }
            $string .= "</telework>";

            $string .= "<onsite>";
            if ( strtolower( $templateVO->isOnsite() ) == 1 ) {
                $string .= "true";
            } else {
                $string .= "false";
            }
            $string .= "</onsite></template>";
        }
    }
    $string .= "</templates>";

} while(false);

// make it into a proper XML document with header etc
$xml = simplexml_load_string($string);

// send an XML mime header
header("Content-type: text/xml");

// output correctly formatted XML
echo $xml->asXML();
