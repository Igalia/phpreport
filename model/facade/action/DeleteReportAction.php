<?php

/** File for DeleteReportAction
 *
 *  This file just contains {@link DeleteReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskVO.php');

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
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        if (!$dao->checkTaskUserId($this->task->getId(), $this->task->getUserId()))
            return -1;

        if ($dao->delete($this->task)!=1) {
            return -1;
        }

        return 0;
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
