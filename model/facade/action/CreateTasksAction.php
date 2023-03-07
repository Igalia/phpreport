<?php
/*
 * Copyright (C) 2013 Igalia, S.L. <info@igalia.com>
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


/** File for CreateTasksAction
 *
 *  This file just contains {@link CreateTasksAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

/** Create Tasks Action
 *
 *  This action is used for creating multiple Task objects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class CreateTasksAction extends Action {

    /** The Task
     *
     * This variable contains an array with the TaskVO objects we want to create.
     *
     * @var array<TaskVO>
     */
    private $tasks;

    /** CreateTasksAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param array $tasks an array with the TaskVO objects we want to create.
     */
    public function __construct($tasks) {
        $this->tasks=$tasks;
        $this->preActionParameter="CREATE_TASKS_PREACTION";
        $this->postActionParameter="CREATE_TASKS_POSTACTION";

    }

    /** Specific code execute.
     *
     * Runs the action itself.
     *
     * @return array OperationResult the array of {@link OperationResult} with information about operation status
     */
    protected function doExecute() {
        $configDao = DAOFactory::getConfigDAO();
        $taskDao = DAOFactory::getTaskDAO();
        $projectDAO = DAOFactory::getProjectDAO();
        $discardedTasks = array();
        $discardedResults = array();

        //first check permission on task write
        foreach ($this->tasks as $i => $task) {
            if (!$configDao->isWriteAllowedForDate($task->getDate())) {
                $result = new OperationResult(false);
                $result->setErrorNumber(20);
                $result->setResponseCode(403);
                $result->setMessage("Error creating task:\nNot allowed to write to date.");
                $discardedResults[] = $result;
                $discardedTasks[] = $task;
                unset($this->tasks[$i]);
                continue;
            }
            $projectVO = $projectDAO->getById($task->getProjectId());
            if (!$projectVO || !$projectVO->getActivation()) {
                $result = new OperationResult(false);
                $result->setErrorNumber(30);
                $result->setResponseCode(403);
                $result->setMessage("Error creating task:\nNot allowed to write to project.");
                $discardedTasks[] = $task;
                $discardedResults[] = $result;
                unset($this->tasks[$i]);
            }
        }

        $results = $taskDao->batchCreate($this->tasks);

        //TODO: do something meaningful with the list of discarded tasks
        if (!empty($discardedTasks)) {
            return array_merge($discardedResults, $results);
        }
        return $results;
    }

}
