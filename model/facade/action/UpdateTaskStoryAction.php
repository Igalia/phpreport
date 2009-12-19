<?php

/** File for UpdateTaskStoryAction
 *
 *  This file just contains {@link UpdateTaskStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

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
