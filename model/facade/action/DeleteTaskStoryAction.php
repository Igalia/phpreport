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


/** File for DeleteTaskStoryAction
 *
 *  This file just contains {@link DeleteTaskStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');

/** Delete Task Story Action
 *
 *  This action is used for deleting a Task Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteTaskStoryAction extends Action{

    /** The Task Story
     *
     * This variable contains the Task Story we want to delete.
     *
     * @var TaskStoryVO
     */
    private $taskStory;

    /** DeleteTaskStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to delete.
     */
    public function __construct(TaskStoryVO $taskStory) {
        $this->taskStory=$taskStory;
        $this->preActionParameter="DELETE_TASK_STORY_PREACTION";
        $this->postActionParameter="DELETE_TASK_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Task Story from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskStoryDAO();
        if ($dao->delete($this->taskStory)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$taskStoryvo= new TaskStoryVO();
$taskStoryvo->setId(1);
$action= new DeleteTaskStoryAction($taskStoryvo);
var_dump($action);
$action->execute();
var_dump($taskStoryvo);
*/
