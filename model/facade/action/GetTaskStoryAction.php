<?php

/** File for GetTaskStoryAction
 *
 *  This file just contains {@link GetTaskStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Task Story Action
 *
 *  This action is used for retrieving a Task Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetTaskStoryAction extends Action{

    /** The Task Story id
     *
     * This variable contains the id of the Task Story we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetTaskStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Task Story we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_TASK_STORY_PREACTION";
        $this->postActionParameter="GET_TASK_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Story from persistent storing.
     *
     * @return TaskStoryVO the Task Story as a {@link TaskStoryVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskStoryDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetTaskStoryAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
