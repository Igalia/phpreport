<?php

/** File for GetCustomStoryAction
 *
 *  This file just contains {@link GetCustomStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/GetIterationCustomStoriesAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomStoryVO.php');
include_once('phpreport/model/vo/CustomTaskStoryVO.php');


/** Get Custom Story Action
 *
 *  This action is used for retrieving a Custom Story by it's id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomStoryAction extends GetIterationCustomStoriesAction{

    /** The Story Id
     *
     * This variable contains the id of the Custom Story we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetCustomStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Custom Story we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_CUSTOM_STORY_PREACTION";
        $this->postActionParameter="GET_CUSTOM_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Story from persistent storing.
     *
     * @return CustomStoryVO a custom object {@link CustomStoryVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();

    $stories[] = $dao->getById($this->storyId);

    if ($stories[0] == NULL)
        return NULL;

    $customStories = $this->StoriesToCustomStories($stories);

    return $customStories[0];

    }

}


/*//Test code;

$action= new GetCustomStoryAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
