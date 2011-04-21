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


/** File for GetCustomTaskStoryAction
 *
 *  This file just contains {@link GetStoryTaskStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryCustomTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskStoryVO.php');


/** Get Custom Task Story Action
 *
 *  This action is used for retrieving a custom Task Story (Task Story with additional data) by it's id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomTaskStoryAction extends GetStoryCustomTaskStoriesAction {

    /** The Task Story Id
     *
     * This variable contains the id of the Custom Task Story we want to retieve.
     *
     * @var int
     */
    private $taskStoryId;

    /** GetCustomTaskStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $taskStoryId the id of the Custom Task Story we want to retieve.
     */
    public function __construct($taskStoryId) {
        $this->taskStoryId=$taskStoryId;
        $this->preActionParameter="GET_CUSTOM_TASK_STORY_PREACTION";
        $this->postActionParameter="GET_CUSTOM_TASK_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Story from persistent storing and calls the function
     * that creates the Custom Object.
     *
     * @return CustomTaskStoryVO a custom object {@link CustomTaskStoryVO} with its properties set to the values from the rows
     * and with additional data.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskStoryDAO();

    $taskStories[] = $dao->getById($this->taskStoryId);

    if ($taskStories[0] == NULL)
        return NULL;

    $customTaskStories = $this->TaskStoriesToCustomTaskStories($taskStories);

    return $customTaskStories[0];

    }

}


/*//Test code;

$action= new GetCustomTaskStoryAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
