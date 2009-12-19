<?php

/** File for UpdateReportAction
 *
 *  This file just contains {@link UpdateReportAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskVO.php');

/** Update Report Action
 *
 *  This action is used for updating a Task.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateReportAction extends Action{

    /** The Task
     *
     * This variable contains the Task we want to update.
     *
     * @var TaskVO
     */
    private $task;

    /** UpdateReportAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param TaskVO $task the Task value object we want to update.
     */
    public function __construct(TaskVO $task) {
        $this->task=$task;
        $this->preActionParameter="UPDATE_REPORT_PREACTION";
        $this->postActionParameter="UPDATE_REPORT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Task on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskDAO();

        if (!$dao->checkTaskUserId($this->task->getId(), $this->task->getUserId()))
            return -1;

        if ($dao->update($this->task)!=1)
            return -1;

        return 0;
    }

}

/*
//Test code
$taskvo = new TaskVO();

$taskvo->setId(1);

$taskvo->setDate(date_create('2010-04-23'));
$taskvo->setInit(1);
$taskvo->setEnd(2);
$taskvo->setStory("Very bad :'(");
$taskvo->setTelework("FALSE");
$taskvo->setText("Old text");
$taskvo->setTtype("Ttype 1");
$taskvo->setPhase("Initial");
$taskvo->setUserId(1);
$taskvo->setProjectId(1);
$taskvo->setTaskId(1);

$action= new UpdateReportAction($taskvo);
var_dump($action);
$action->execute();
var_dump($taskvo);
*/
