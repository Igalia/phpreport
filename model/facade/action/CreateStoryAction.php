<?php

/** File for CreateStoryAction
 *
 *  This file just contains {@link CreateStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/StoryVO.php');

/** Create Story Action
 *
 *  This action is used for creating a new Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateStoryAction extends Action{

    /** The Story
     *
     * This variable contains the Story we want to create.
     *
     * @var StoryVO
     */
    private $project;

    /** CreateStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param StoryVO $story the Story value object we want to create.
     */
    public function __construct(StoryVO $story) {
        $this->story=$story;
        $this->preActionParameter="CREATE_STORY_PREACTION";
        $this->postActionParameter="CREATE_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Story, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();
        if ($dao->create($this->story)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$storyvo = new StoryVO;

$storyvo->setName("Bring the crates");
$storyvo->setAccepted(False);
$storyvo->setIterationId(1);
$storyvo->setUserId(1);

$action= new CreateStoryAction($storyvo);
var_dump($action);
$action->execute();
var_dump($storyvo);*/
