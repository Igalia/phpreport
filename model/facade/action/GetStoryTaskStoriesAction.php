<?php

/** File for GetStoryTaskStoriesAction
 *
 *  This file just contains {@link GetStoryTaskStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/StoryVO.php');


/** Get Story Task Stories Action
 *
 *  This action is used for retrieving all Task Stories related to a Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryTaskStoriesAction extends Action{

    /** The Story Id
     *
     * This variable contains the id of the Story whose Task Stories we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetStoryTaskStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Story whose Task Stories we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_STORY_TASK_STORIES_PREACTION";
        $this->postActionParameter="GET_STORY_TASK_STORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Stories from persistent storing.
     *
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskStoryDAO();

        return $dao->getByStoryId($this->storyId);

    }

}


/*//Test code;

$action= new GetStoryTaskStoriesAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
