<?php

/** File for GetTaskSectionAction
 *
 *  This file just contains {@link GetTaskSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Task Section Action
 *
 *  This action is used for retrieving a Task Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetTaskSectionAction extends Action{

    /** The Task Section id
     *
     * This variable contains the id of the Task Section we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetTaskSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Task Section we want to retieve.
     */
    public function __construct($id) {
        $this->id = $id;
        $this->preActionParameter="GET_TASK_SECTION_PREACTION";
        $this->postActionParameter="GET_TASK_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Section from persistent storing.
     *
     * @return TaskSectionVO the Task Section as a {@link TaskSectionVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskSectionDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetTaskSectionAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
