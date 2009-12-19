<?php

/** File for GetTaskSectionTaskStoriesAction
 *
 *  This file just contains {@link GetTaskSectionTaskStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskSectionVO.php');


/** Get Task Section Task Stories Action
 *
 *  This action is used for retrieving all Task Stories related to a Task Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetTaskSectionTaskStoriesAction extends Action{

    /** The TaskSection Id
     *
     * This variable contains the id of the TaskSection whose Task Stories we want to retieve.
     *
     * @var int
     */
    private $taskSectionId;

    /** GetTaskSectionTaskStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $taskSectionId the id of the Task Section whose Task Stories we want to retieve.
     */
    public function __construct($taskSectionId) {
        $this->taskSectionId=$taskSectionId;
        $this->preActionParameter="GET_TASK_SECTION_TASK_STORIES_PREACTION";
        $this->postActionParameter="GET_TASK_SECTION_TASK_STORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Stories from persistent storing.
     *
     * @return array an array with value objects {@link TaskTaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskStoryDAO();

        return $dao->getByTaskSectionId($this->taskSectionId);

    }

}


/*//Test code;

$action= new GetTaskSectionTaskStoriesAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
