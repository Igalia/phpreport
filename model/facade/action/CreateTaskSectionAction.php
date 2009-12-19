<?php

/** File for CreateTaskSectionAction
 *
 *  This file just contains {@link CreateTaskSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskSectionVO.php');

/** Create Task Section Action
 *
 *  This action is used for creating a new Task Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateTaskSectionAction extends Action{

    /** The Task Section
     *
     * This variable contains the Task Section we want to create.
     *
     * @var TaskSectionVO
     */
    private $project;

    /** CreateTaskSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskSectionVO $taskSection the Task Section value object we want to create.
     */
    public function __construct(TaskSectionVO $taskSection) {
        $this->taskSection=$taskSection;
        $this->preActionParameter="CREATE_TASK_SECTION_PREACTION";
        $this->postActionParameter="CREATE_TASK_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Task Section, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskSectionDAO();
        if ($dao->create($this->taskSection)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$tasksectionvo = new TaskSectionVO;

$tasksectionvo->setName("Getting the crates");
$tasksectionvo->setEstHours(20);
$tasksectionvo->setRisk(5);
$tasksectionvo->setSectionId(5);

$action= new CreateTaskSectionAction($tasksectionvo);
var_dump($action);
$action->execute();
var_dump($tasksectionvo);
*/
