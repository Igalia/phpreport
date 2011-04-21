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

/** getProjectTrackerTree web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 * @author Jorge López Fernández
 */

    define('PHPREPORT_ROOT', __DIR__ . '/../../');
    include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
    include_once(PHPREPORT_ROOT . '/model/facade/CoordinationFacade.php');

    $projectId = $_GET['pid'];

    // We get the iteration id for returning it expanded
    $iterationId = $_GET['iid'];

    $sid = $_GET['sid'];

    if (!isset($projectId))
        echo json_encode("");
        else
        {

        //$projectId = 1;

        do {

            /* We check authentication and authorization */
            require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

            if (!LoginManager::isLogged($sid))
            {
                $error[id] = 2;
                $error[message] = "You must be logged in";
                $json[error] = $error;
                break;
            }

            if (!LoginManager::isAllowed($sid))
            {
                $error[id] = 3;
                $error[message] = "Forbidden service for this User";
                $json[error] = $error;
                break;
            }

            $iterations = CoordinationFacade::GetProjectIterations($projectId);

            foreach((array) $iterations as $iteration)
            {
            $iterationJson = array();

            $iterationJson[id] = "iteration-{$iteration->getId()}";
            $iterationJson[internalId] = "{$iteration->getId()}";
            if ($iteration->getId() == $iterationId)
                $iterationJson[expanded] = true;
            $iterationJson[task] = $iteration->getName();
            $iterationJson[summary] = $iteration->getSummary();

            if (!is_null($iteration->getInit()))
                $iterationJson[init] = $iteration->getInit()->format("Y-m-d");

            if (!is_null($iteration->getEnd()))
                $iterationJson[end] = $iteration->getEnd()->format("Y-m-d");

            $iterationChildren = array();

            $stories = CoordinationFacade::GetIterationCustomStories($iteration->getId());

            foreach ((array) $stories as $story)
            {

                $storyJson = array();

                $storyJson[id] = "story-{$story->getId()}";
                $storyJson[internalId] = "{$story->getId()}";
                $storyJson[accepted] = $story->getAccepted();
                $storyJson[task] = $story->getName();
                $storyJson[duration] = $story->getEstHours();
                $storyJson[spent] = $story->getSpent();
                $storyJson[done] = $story->getDone();
                $storyJson[overrun] = $story->getOverrun();
                $storyJson[toDo] = $story->getToDo();
                $storyJson[nextStoryId] = $story->getStoryId();

                $taskStories = CoordinationFacade::GetStoryCustomTaskStories($story->getId());

                $storyChildren = array();

                foreach((array) $taskStories as $taskStory)
                {
                    $taskStoryJson = array();

                    $taskStoryJson[id] = "taskStory-{$taskStory->getId()}";
                    $taskStoryJson[internalId] = "{$taskStory->getId()}";
                    $taskStoryJson[risk] = $taskStory->getRisk();
                    $taskStoryJson[task] = $taskStory->getName();
                    $taskStoryJson[duration] = $taskStory->getEstHours();
                    $taskStoryJson[spent] = $taskStory->getSpent();
                    $taskStoryJson[toDo] = $taskStory->getToDo();

                    $developer = $taskStory->getDeveloper();

                    if ($developer)
                        $taskStoryJson[user] = $developer->getLogin();

                    if (!is_null($taskStory->getInit()))
                        $taskStoryJson[init] = $taskStory->getInit()->format("Y-m-d");

                    if (!is_null($taskStory->getEnd()))
                        $taskStoryJson[end] = $taskStory->getEnd()->format("Y-m-d");

                    if (!is_null($taskStory->getEstEnd()))
                    {
                        $date[format] = 'Y-m-d';
                        $date[value] = $taskStory->getEstEnd()->format("Y-m-d");
                        $taskStoryJson[estEnd] = $date;
                    }

                    $taskStoryJson[uiProvider] = 'col';
                    $taskStoryJson[leaf] = true;
                    $taskStoryJson[iconCls] = 'task';
                    $taskStoryJson['class'] = 'task-story';

                    $storyChildren[] = $taskStoryJson;

                }

                $storyJson[children] = $storyChildren;

                $storyJson[uiProvider] = 'col';
                $storyJson[leaf] = false;
                $storyJson[iconCls] = 'task';
                $storyJson['class'] = 'story';

                $iterationChildren[] = $storyJson;

            }

            $iterationJson[children] = $iterationChildren;

            $iterationJson[uiProvider] = 'col';
            $iterationJson[cls] = 'master-task';
            $iterationJson[leaf] = false;
            $iterationJson[iconCls] = 'task-folder';
            $iterationJson['class'] = 'iteration';

            $json[] = $iterationJson;

            }

        } while (False);

        if ($json == NULL)
                    $json = '';

        echo json_encode($json);
    }
