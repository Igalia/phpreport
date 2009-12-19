<?php

/** File for GetIterationStoriesAction
 *
 *  This file just contains {@link GetIterationStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/IterationVO.php');


/** Get Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetIterationStoriesAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Stories we want to retieve.
     *
     * @var int
     */
    private $iterationId;

    /** GetIterationStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $iterationId the id of the Iteration whose Stories we want to retieve.
     */
    public function __construct($iterationId) {
        $this->iterationId=$iterationId;
        $this->preActionParameter="GET_ITERATION_STORIES_PREACTION";
        $this->postActionParameter="GET_ITERATION_STORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Stories from persistent storing.
     *
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();

        return $dao->getByIterationId($this->iterationId);

    }

}


/*//Test code;

$action= new GetIterationStoriesAction(8);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
