<?php

/** File for GetStoryAction
 *
 *  This file just contains {@link GetStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Story Action
 *
 *  This action is used for retrieving a Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryAction extends Action{

    /** The Story id
     *
     * This variable contains the id of the Story we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Story we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_STORY_PREACTION";
        $this->postActionParameter="GET_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Story from persistent storing.
     *
     * @return StoryVO the Story as a {@link StoryVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetStoryAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
