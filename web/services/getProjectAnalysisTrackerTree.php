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

/** getProjectAnalysisTrackerTree web service.
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

    // We get the module id for returning it expanded
    $moduleId = $_GET['mid'];

    $login = $_GET['login'];

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

            $modules = CoordinationFacade::GetProjectModules($projectId);

            foreach((array) $modules as $module)
            {
            $moduleJson = array();

            $moduleJson[id] = "module-{$module->getId()}";
            $moduleJson[internalId] = "{$module->getId()}";
            if ($module->getId() == $moduleId)
                $moduleJson[expanded] = true;
            $moduleJson[task] = $module->getName();
            $moduleJson[summary] = $module->getSummary();

            if (!is_null($module->getInit()))
                $moduleJson[init] = $module->getInit()->format("Y-m-d");

            if (!is_null($module->getEnd()))
                $moduleJson[end] = $module->getEnd()->format("Y-m-d");

            $moduleChildren = array();

            $sections = CoordinationFacade::GetModuleCustomSections($module->getId());

            foreach ((array) $sections as $section)
            {

                $sectionJson = array();

                $sectionJson[id] = "section-{$section->getId()}";
                $sectionJson[internalId] = "{$section->getId()}";
                $sectionJson[accepted] = $section->getAccepted();
                $sectionJson[task] = $section->getName();
                $sectionJson[duration] = $section->getEstHours();
                $sectionJson[spent] = $section->getSpent();
                $sectionJson[done] = $section->getDone();
                $sectionJson[overrun] = $section->getOverrun();
                $sectionJson[toDo] = $section->getToDo();

                $taskSections = CoordinationFacade::GetSectionCustomTaskSections($section->getId());

                $sectionChildren = array();

                foreach((array) $taskSections as $taskSection)
                {
                    $taskSectionJson = array();

                    $taskSectionJson[id] = "taskSection-{$taskSection->getId()}";
                    $taskSectionJson[internalId] = "{$taskSection->getId()}";
                    $taskSectionJson[risk] = $taskSection->getRisk();
                    $taskSectionJson[task] = $taskSection->getName();
                    $taskSectionJson[duration] = $taskSection->getEstHours();
                    $taskSectionJson[spent] = $taskSection->getSpent();
                    $taskSectionJson[toDo] = $taskSection->getToDo();

                    $developer = $taskSection->getDeveloper();

                    if ($developer)
                        $taskSectionJson[user] = $developer->getLogin();

                    $taskSectionJson[uiProvider] = 'col';
                    $taskSectionJson[leaf] = true;
                    $taskSectionJson[iconCls] = 'task';
                    $taskSectionJson['class'] = 'task-section';

                    $sectionChildren[] = $taskSectionJson;

                }

                $sectionJson[children] = $sectionChildren;

                $sectionJson[uiProvider] = 'col';
                $sectionJson[leaf] = false;
                $sectionJson[iconCls] = 'task';
                $sectionJson['class'] = 'section';

                $moduleChildren[] = $sectionJson;

            }

            $moduleJson[children] = $moduleChildren;

            $moduleJson[uiProvider] = 'col';
            $moduleJson[cls] = 'master-task';
            $moduleJson[leaf] = false;
            $moduleJson[iconCls] = 'task-folder';
            $moduleJson['class'] = 'module';

            $json[] = $moduleJson;

            }

        } while (False);

        if ($json == NULL)
                    $json = '';

        echo json_encode($json);
    }
