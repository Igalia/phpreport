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


/** File for DeleteReportAction
 *
 *  This file just contains {@link DeleteReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');
include_once(PHPREPORT_ROOT . '/model/OperationResult.php');

/** Delete Task Action
 *
 *  This action is used for deleting a Task.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteReportAction extends Action{

    /** The Task
     *
     * This variable contains the Task we want to delete.
     *
     * @var TaskVO
     */
    private $task;

    /** DeleteReportAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskVO $task the Task value object we want to delete.
     */
    public function __construct(TaskVO $task) {
        $this->task=$task;
        $this->preActionParameter="DELETE_REPORT_PREACTION";
        $this->postActionParameter="DELETE_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Task from persistent storing.
     *
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    protected function doExecute() {
        //first check if current configuration allows deleting tasks in that date
        $configDao = DAOFactory::getConfigDAO();
        if(!$configDao->isWriteAllowedForDate($this->task->getDate())) {
            $result = new OperationResult(false);
            $result->setErrorNumber(20);
            $result->setResponseCode(403);
            $result->setMessage("Error deleting task:\nNot allowed to write to date.");
            return $result;
        }

        $dao = DAOFactory::getTaskDAO();

        if (!$dao->checkTaskUserId($this->task->getId(), $this->task->getUserId())) {
            $result = new OperationResult(false);
            $result->setErrorNumber(50);
            $result->setResponseCode(403);
            $result->setMessage("Error deleting task:\nBelongs to a different user.");
            return $result;
        }

        // Do not allow deleting tasks which belong to inactive projects
        $oldTask = $dao->getById($this->task->getId());
        $projectId = $oldTask->getProjectId();
        $projectVO = DAOFactory::getProjectDAO()->getById($projectId);
        if (!$projectVO || !$projectVO->getActivation()) {
            $result = new OperationResult(false);
            $result->setErrorNumber(30);
            $result->setResponseCode(403);
            $result->setMessage("Error updating task:\nNot allowed to write to project.");
            return $result;
        }

        return $dao->delete($this->task);
    }

}


/*//Test code
$taskvo = new TaskVO();

$taskvo->setId(124270);

$action= new DeleteReportAction($taskvo);
var_dump($action);
$action->execute();
var_dump($taskvo);
*/
