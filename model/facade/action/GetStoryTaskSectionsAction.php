<?php

/** File for GetStoryTaskSectionsAction
 *
 *  This file just contains {@link GetStoryTaskSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/TaskVO.php');


/** Get Story Task Sections Action
 *
 *  This action is used for retrieving all Task Sections related to a Story through its Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryTaskSectionsAction extends Action{

    /** The Story Id
     *
     * This variable contains the id of the Story whose TaskSections we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetStoryTaskSectionsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Story whose TaskSections we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_STORY_TASK_SECTIONS_PREACTION";
        $this->postActionParameter="GET_STORY_TASK_SECTIONS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the TaskSections from persistent storing.
     *
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskSectionDAO();

        return $dao->getByStoryId($this->storyId);

    }

}


/*//Test code;

$action= new GetStoryTaskSectionsAction(3);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
