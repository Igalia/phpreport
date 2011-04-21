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


/** File for UpdateTaskStoryAction
 *
 *  This file just contains {@link UpdateTaskStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Update Task Story Action
 *
 *  This action is used for updating a Task Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateTaskStoryAction extends Action{

    /** The Task Story
     *
     * This variable contains the Task Story we want to update.
     *
     * @var TaskStoryVO
     */
    private $taskStory;

    /** UpdateTaskStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to update.
     */
    public function __construct(TaskStoryVO $taskStory) {
        $this->taskStory=$taskStory;
        $this->preActionParameter="UPDATE_TASK_STORY_PREACTION";
        $this->postActionParameter="UPDATE_TASK_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Task Story on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskStoryDAO();
        if ($dao->update($this->taskStory)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$taskStoryvo= new TaskStoryVO();
$taskStoryvo->setId(1);
$taskStoryvo->setName('Pizza Deliverers');
$action= new UpdateTaskStoryAction($taskStoryvo);
var_dump($action);
$action->execute();
var_dump($taskStoryvo);
*/
