<?php

/** File for GetStoryTasksAction
 *
 *  This file just contains {@link GetStoryTasksAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskVO.php');


/** Get Story Tasks Action
 *
 *  This action is used for retrieving all Tasks related to a Story through its Task Stories.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryTasksAction extends Action{

    /** The Story Id
     *
     * This variable contains the id of the Story whose Tasks we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetStoryTasksAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Story whose Tasks we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_STORY_TASKS_PREACTION";
        $this->postActionParameter="GET_STORY_TASKS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Tasks from persistent storing.
     *
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskDAO();

        return $dao->getByStoryId($this->storyId);

    }

}


/*//Test code;

$action= new GetStoryTaskStoriesAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
