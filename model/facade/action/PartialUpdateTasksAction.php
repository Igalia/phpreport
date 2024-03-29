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


/** File for PartialUpdateTasksAction
 *
 *  This file just contains {@link PartialUpdateTasksAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');

/** Partial Update Tasks Action
 *
 *  This action is used for updating only some fields of a set of Task objects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class PartialUpdateTasksAction extends Action{

    /** The Task
     *
     * This variable contains an array with the Task objects we want to update.
     * The elements of the array must be DirtyTaskVO objects to contain the
     * information about which fields must be updated.
     *
     * @var array<DirtyTaskVO>
     */
    private $tasks;

    /** PartialUpdateTasksAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param array $tasks an array with the Task objects we want to update.
     * The elements of the array must be DirtyTaskVO objects to contain the
     * information about which fields must be updated.
     */
    public function __construct($tasks) {
        $this->tasks=$tasks;
        $this->preActionParameter="PARTIAL_UPDATE_TASKS_PREACTION";
        $this->postActionParameter="PARTIAL_UPDATE_TASKS_POSTACTION";

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
            // Do not allow assigning a task to a locked date
            if ($task->isDateDirty()) {
                if(!$configDao->isWriteAllowedForDate($task->getDate())) {
                    $result = new OperationResult(false);
                    $result->setErrorNumber(20);
                    $result->setResponseCode(403);
                    $result->setMessage("Error updating task:\nNot allowed to write to date.");
                    $discardedResults[] = $result;
                    $discardedTasks[] = $task;
                    unset($this->tasks[$i]);
                    continue;
                }
            }

            $oldTask = $taskDao->getById($task->getId());
            if (!isset($oldTask)) {
                $result = new OperationResult(false);
                $result->setErrorNumber(40);
                $result->setResponseCode(400);
                $result->setMessage("Error updating task:\nTask does not exist.");
                $discardedResults[] = $result;
                $discardedTasks[] = $task;
                unset($this->tasks[$i]);
                continue;
            }

            // Do not allow updating tasks saved in locked dates
            if(!$configDao->isWriteAllowedForDate($oldTask->getDate())) {
                $result = new OperationResult(false);
                $result->setErrorNumber(20);
                $result->setResponseCode(403);
                $result->setMessage("Error updating task:\nNot allowed to write to date.");
                $discardedResults[] = $result;
                $discardedTasks[] = $task;
                unset($this->tasks[$i]);
                continue;
            }

            // Do not allow updating tasks belonging to a different user
            if(!$taskDao->checkTaskUserId(
                        $task->getId(), $task->getUserId())) {
                $result = new OperationResult(false);
                $result->setErrorNumber(50);
                $result->setResponseCode(403);
                $result->setMessage("Error updating task:\nBelongs to a different user.");
                $discardedResults[] = $result;
                $discardedTasks[] = $task;
                unset($this->tasks[$i]);
                continue;
            }

            // Do not allow assigning a task to an inactive project
            if ($task->isProjectIdDirty()) {
                $projectId = $task->getProjectId();
                $projectVO = $projectDAO->getById($projectId);
                if (!$projectVO || !$projectVO->getActivation()) {
                    $result = new OperationResult(false);
                    $result->setErrorNumber(30);
                    $result->setResponseCode(403);
                    $result->setMessage("Error updating task:\nNot allowed to write to project.");
                    $discardedResults[] = $result;
                    $discardedTasks[] = $task;
                    unset($this->tasks[$i]);
                    continue;
                }
            }

            // Do not allow updating tasks which belong to inactive projects
            $projectId = $oldTask->getProjectId();
            $projectVO = $projectDAO->getById($projectId);
            if (!$projectVO || !$projectVO->getActivation()) {
                $result = new OperationResult(false);
                $result->setErrorNumber(30);
                $result->setResponseCode(403);
                $result->setMessage("Error updating task:\nNot allowed to write to project.");
                $discardedResults[] = $result;
                $discardedTasks[] = $task;
                unset($this->tasks[$i]);
                continue;
            }

            if ($task->isInitDirty() & !$task->isEndDirty()) {
                $currentEndTime = $oldTask->getEnd();
                $newInitTime = $task->getInit();
                // Check if init was updated and endTime should be converted to 24:00
                // (in case it was a 0-hour task and it's not anymore)
                if ($newInitTime > $currentEndTime && $currentEndTime === 0) {
                    $task->setEnd(1440);
                }

                // Check if init was updated and endTime should be converted to 0h
                // (in case it was a regular task and now it's a 0-hour task)
                if ($newInitTime === 0 && $currentEndTime == 1440) {
                    $task->setEnd(0);
                }
            } elseif ($task->isEndDirty()) {
                $currentInitTime = $oldTask->getInit();
                $newEndTime = $task->getEnd();
                // Check if end was updated and it should be converted to 0
                // (in case it became a 0-hour task)
                if ($currentInitTime === 0 && $newEndTime === 1440) {
                    $task->setEnd(0);
                }
            }
        }

        $results = $taskDao->batchPartialUpdate($this->tasks);

        //TODO: do something meaningful with the list of discarded tasks
        if (!empty($discardedTasks)) {
            return array_merge($discardedResults, $results);
        }
        return $results;
    }

}
