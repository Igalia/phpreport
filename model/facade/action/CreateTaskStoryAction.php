<?php

/** File for CreateTaskStoryAction
 *
 *  This file just contains {@link CreateTaskStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskStoryVO.php');

/** Create Task Story Action
 *
 *  This action is used for creating a new Task Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateTaskStoryAction extends Action{

    /** The Task Story
     *
     * This variable contains the Task Story we want to create.
     *
     * @var TaskStoryVO
     */
    private $project;

    /** CreateTaskStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to create.
     */
    public function __construct(TaskStoryVO $taskStory) {
        $this->taskStory=$taskStory;
        $this->preActionParameter="CREATE_TASK_STORY_PREACTION";
        $this->postActionParameter="CREATE_TASK_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Task Story, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskStoryDAO();
        if ($dao->create($this->taskStory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$taskstoryvo = new TaskStoryVO;

$taskstoryvo->setName("Getting the crates");
$taskstoryvo->setInit(date_create('2009-06-01'));
$taskstoryvo->setEnd(date_create('2009-06-05'));
$taskstoryvo->setEstEnd(date_create('2009-06-02'));
$taskstoryvo->setEstHours(20);
$taskstoryvo->setRisk(5);
$taskstoryvo->setStoryId(5);

$action= new CreateTaskStoryAction($taskstoryvo);
var_dump($action);
$action->execute();
var_dump($taskstoryvo);
*/
