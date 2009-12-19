<?php

/** File for UpdateTaskSectionAction
 *
 *  This file just contains {@link UpdateTaskSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Update Task Section Action
 *
 *  This action is used for updating a Task Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateTaskSectionAction extends Action{

    /** The Task Section
     *
     * This variable contains the Task Section we want to update.
     *
     * @var TaskSectionVO
     */
    private $taskSection;

    /** UpdateTaskSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskSectionVO $taskSection the Task Section value object we want to update.
     */
    public function __construct(TaskSectionVO $taskSection) {
        $this->taskSection=$taskSection;
        $this->preActionParameter="UPDATE_TASK_SECTION_PREACTION";
        $this->postActionParameter="UPDATE_TASK_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Task Section on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskSectionDAO();
        if ($dao->update($this->taskSection)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$taskSectionvo= new TaskSectionVO();
$taskSectionvo->setId(1);
$taskSectionvo->setName('Pizza Deliverers');
$action= new UpdateTaskSectionAction($taskSectionvo);
var_dump($action);
$action->execute();
var_dump($taskSectionvo);
*/
