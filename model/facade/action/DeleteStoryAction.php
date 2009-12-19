<?php

/** File for DeleteStoryAction
 *
 *  This file just contains {@link DeleteStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/StoryVO.php');

/** Delete Story Action
 *
 *  This action is used for deleting an Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteStoryAction extends Action{

    /** The Story
     *
     * This variable contains the Story we want to delete.
     *
     * @var StoryVO
     */
    private $story;

    /** DeleteStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param StoryVO $story the Story value object we want to delete.
     */
    public function __construct(SectorVO $story) {
        $this->story=$story;
        $this->preActionParameter="DELETE_STORY_PREACTION";
        $this->postActionParameter="DELETE_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Story from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getStoryDAO();
        if ($dao->delete($this->story)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$storyvo= new StoryVO();
$storyvo->setId(1);
$action= new DeleteStoryAction($storyvo);
var_dump($action);
$action->execute();
var_dump($storyvo);
*/
