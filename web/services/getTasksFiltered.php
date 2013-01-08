<?php
/*
 * Copyright (C) 2010 Igalia, S.L. <info@igalia.com>
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

/** getTasksFiltered web service.
 *
 * This file runs the service to retrieve tasks filtered by multiple fields.
 * It's invoked through GET with the following parameters:
 * <ul>
 *   <li>
 *     <b>filterStartDate</b> start date to filter tasks. Those tasks having a
 *     date equal or later than this one will be returned. The date format is
 *     specified by the parameter dateFormat.
 *   </li>
 *   <li>
 *     <b>filterEndDate</b> end date to filter tasks. Those tasks having a date
 *     equal or sooner than this one will be returned. The date format is
 *     specified by the parameter dateFormat.
 *   </li>
 *   <li>
 *     <b>dateFormat</b> format used to input the parameters filterStartDate and
 *     filterEndDate. It uses the PHP format strings as specified in
 *     http://www.php.net/manual/function.date.php. Its default value is Y-m-d.
 *   </li>
 *   <li>
 *     <b>telework</b> filter tasks by their telework field. Values can be 'true'
 *     or 'false'.
 *   </li>
 *   <li>
 *     <b>onsite</b> filter tasks by their onsite field. Values can be 'true'
 *     or 'false'.
 *   </li>
 *   <li>
 *     <b>filterText</b> string to filter tasks by their description field.
 *     Tasks with a description that contains this string will be returned.
 *   </li>
 *   <li>
 *     <b>type</b> string to filter projects by their type field. Only projects
 *     with a type field that matches completely with this string will be
 *     returned.
 *   </li>
 *   <li>
 *     <b>userId</b> id of the user whose tasks will be filtered.
 *   </li>
 *   <li>
 *     <b>projectId</b> id of the project which tasks will be filtered by.
 *   </li>
 *   <li>
 *     <b>customerId</b> id of the customer whose tasks will be filtered.
 *   </li>
 *   <li>
 *     <b>taskStoryId</b> id of the story inside the XP tracker which tasks will
 *     be filtered.
 *   </li>
 *   <li>
 *     <b>filterStory</b> string to filter tasks by their story field. Tasks
 *     with a story that contains this string will be returned.
 *   </li>
 *   <li>
 *     <b>emptyText</b> filter tasks by the presence, or absence, of text in
 *     the description field. Values can be 'true' or 'false'. If this field is
 *     present, the parameter filterText will be ignored.
 *   </li>
 *   <li>
 *     <b>emptyStory</b> filter tasks by the presence, or absence, of text in
 *     the story field. Values can be 'true' or 'false'. If this field is
 *     present, the parameter filterStory will be ignored.
 *   </li>
 * </ul>
 * In case the operation is successfull, it will return the tasks in a XML
 * string formatted like this:
 * <code>
 * <?xml version="1.0"?>
 * <tasks>
 *   <task>
 *     <id>1</id>
 *     <date format="Y-m-d">2010-11-18</date>
 *     <initTime>16:00</initTime>
 *     <endTime>16:15</endTime>
 *     <story>Iteration01Coordination</story>
 *     <telework>true</telework>
 *     <ttype>coordination</ttype>
 *     <text>Task description</text>
 *     <phase/>
 *     <userId>1</userId>
 *     <projectId>1</projectId>
 *     <customerId>1</customerId>
 *     <taskStoryId/>
 *   </task>
 *   ...
 * </tasks>
 * </code>
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

   define('PHPREPORT_ROOT', __DIR__ . '/../../');
   include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
   include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
   include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

    $sid = $_GET['sid'];

    do {
        /* We check authentication and authorization */
        require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

        if (!LoginManager::isLogged($sid))
        {
            $string = "<tasks><error id='2'>You must be logged in</error></tasks>";
            break;
        }

        if (!LoginManager::isAllowed($sid))
        {
            $string = "<tasks><error id='3'>Forbidden service for this User</error></tasks>";
            break;
        }

        /* Retrieve the data from the request */
        $dateFormat = "Y-m-d";
        $filterStartDate = NULL;
        $filterEndDate = NULL;
        $telework = NULL;
        $onsite = NULL;
        $filterText = NULL;
        $type = NULL;
        $userId = NULL;
        $projectId = NULL;
        $customerId = NULL;
        $taskStoryId = NULL;
        $filterStory = NULL;
        $emptyText = NULL;
        $emptyStory = NULL;
        if (isset($_GET['dateFormat'])) {
            $dateFormat = $_GET['dateFormat'];
        }
        if (isset($_GET['filterStartDate'])) {
            $filterStartDate = DateTime::createFromFormat(
                    $dateFormat, $_GET['filterStartDate']);
        }
        if (isset($_GET['filterEndDate'])) {
            $filterEndDate = DateTime::createFromFormat(
                    $dateFormat, $_GET['filterEndDate']);
        }
        if (isset($_GET['telework'])) {
            if ($_GET['telework'] == 'true') {
                $telework = true;
            }
            else if ($_GET['telework'] == 'false') {
                $telework = false;
            }
        }
        if (isset($_GET['onsite'])) {
            if ($_GET['onsite'] == 'true') {
                $onsite = true;
            }
            else if ($_GET['onsite'] == 'false') {
                $onsite = false;
            }
        }
        if (isset($_GET['filterText'])) {
            $filterText = $_GET['filterText'];
        }
        if (isset($_GET['type'])) {
            $type = $_GET['type'];
        }
        if (isset($_GET['userId'])) {
            $userId = $_GET['userId'];
        }
        if (isset($_GET['projectId'])) {
            $projectId = $_GET['projectId'];
        }
        if (isset($_GET['customerId'])) {
            $customerId = $_GET['customerId'];
        }
        if (isset($_GET['taskStoryId'])) {
            $taskStoryId = $_GET['taskStoryId'];
        }
        if (isset($_GET['filterStory'])) {
            $filterStory = $_GET['filterStory'];
        }
        if (isset($_GET['emptyText'])) {
            if ($_GET['emptyText'] == 'true') {
                $emptyText = true;
            }
            else if ($_GET['emptyText'] == 'false') {
                $emptyText = false;
            }
        }
        if (isset($_GET['emptyStory'])) {
            if ($_GET['emptyStory'] == 'true') {
                $emptyStory = true;
            }
            else if ($_GET['emptyStory'] == 'false') {
                $emptyStory = false;
            }
        }

        $tasks = TasksFacade::GetTasksFiltered($filterStartDate, $filterEndDate,
                $telework, $onsite, $filterText, $type, $userId, $projectId, $customerId,
                $taskStoryId, $filterStory, $emptyText, $emptyStory);

        $string = "<tasks>";

        foreach((array) $tasks as $task) {
            $string = $string . "<task><id>{$task->getId()}</id>" .
                    "<date format='$dateFormat'>{$task->getDate()->format($dateFormat)}</date>" .
                    "<initTime>" . str_pad(floor($task->getInit()/60), 2, "0", STR_PAD_LEFT) .
                    ":" . str_pad($task->getInit()%60, 2, "0", STR_PAD_LEFT)  . "</initTime>" .
                    "<endTime>" . str_pad(floor($task->getEnd()/60)%24, 2, "0", STR_PAD_LEFT) .
                    ":" . str_pad($task->getEnd()%60, 2, "0", STR_PAD_LEFT)  . "</endTime>" .
                    "<story>" . escape_string($task->getStory()) . "</story>" .
                    "<telework>";

            if ($task->getTelework())
                $string .=  "true";
            else $string .= "false";
            $string .= "</telework><onsite>";
            if ($task->getOnsite())
                $string .=  "true";
            else $string .= "false";

            $string .= "</onsite><ttype>" . escape_string($task->getTtype()) . "</ttype><text>" . escape_string($task->getText()) . "</text><phase>" . escape_string($task->getPhase()) . "</phase><userId>{$task->getUserId()}</userId><projectId>{$task->getProjectId()}</projectId><customerId>{$task->getCustomerId()}</customerId><taskStoryId>{$task->getTaskStoryId()}</taskStoryId></task>";

        }

        $string = $string . "</tasks>";

    } while(false);

   // make it into a proper XML document with header etc
    $xml = simplexml_load_string($string);

   // send an XML mime header
    header("Content-type: text/xml");

   // output correctly formatted XML
    echo $xml->asXML();
