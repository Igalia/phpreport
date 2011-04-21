<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for GetStoryCustomTaskStoriesAction
 *
 *  This file just contains {@link GetStoryTaskStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskStoryVO.php');


/** Get Story Task Stories Action
 *
 *  This action is used for retrieving all custom Task Stories (Task Stories with additional data) related to a Story.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryCustomTaskStoriesAction extends Action {

    /** The Story Id
     *
     * This variable contains the id of the Story whose custom Task Stories we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetStoryCustomTaskStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Story whose Task Stories we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_STORY_CUSTOM_TASK_STORIES_PREACTION";
        $this->postActionParameter="GET_STORY_CUSTOM_TASK_STORIES_POSTACTION";

    }

    /** TaskStoriesToCustomTaskStories function.
     *
     * This function receives an array of value objects {@link TaskStoryVO} and creates their custom objects {@link CustomTaskStoryVO}.
     *
     * @param array $taskStories an array of value objects {@link TaskStoryVO}.
     * @return array an array with custom objects {@link CustomTaskStoryVO} with their properties set to the values from the value
     * objects {@link TaskStoryVO} and with additional data and ordered ascendantly by their database internal identifier
     */
    protected function TaskStoriesToCustomTaskStories($taskStories) {

    $customTaskStories = array();

    foreach ((array) $taskStories as $taskStory)
    {

        $customTaskStory = new CustomTaskStoryVO();

        $customTaskStory->setName($taskStory->getName());

        $customTaskStory->setId($taskStory->getId());

        $customTaskStory->setRisk($taskStory->getRisk());

        $customTaskStory->setEstHours($taskStory->getEstHours());

        $customTaskStory->setEstEnd($taskStory->getEstEnd());

        $customTaskStory->setInit($taskStory->getInit());

        $customTaskStory->setEnd($taskStory->getEnd());

        $customTaskStory->setToDo($taskStory->getToDo());

        $customTaskStory->setStoryId($taskStory->getStoryId());

        if (!is_null($taskStory->getUserId()))
        {

            $dao = DAOFactory::getUserDAO();

            $customTaskStory->setDeveloper($dao->getById($taskStory->getUserId()));

        }

        if (!is_null($taskStory->getTaskSectionId()))
        {

            $dao = DAOFactory::getTaskSectionDAO();

            $customTaskStory->setTaskSection($dao->getById($taskStory->getTaskSectionId()));

        }

        if (!is_null($taskStory->getStoryId()))
        {

            $dao = DAOFactory::getStoryDAO();

            $story = $dao->getById($taskStory->getStoryId());

            if (!is_null($story->getUserId()))
            {

                $dao = DAOFactory::getUserDAO();

                $customTaskStory->setReviewer($dao->getById($story->getUserId()));

            }

        }

        $dao = DAOFactory::getTaskDAO();

        $tasks = $dao->getByTaskStoryId($taskStory->getId());

        $spent = 0.0;

        foreach((array) $tasks as $task)
            $spent += ( $task->getEnd() - $task->getInit() ) / 60.0;


        $customTaskStory->setSpent($spent);

        $customTaskStories[] = $customTaskStory;

    }

    return $customTaskStories;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Stories from persistent storing and calls the function
     * that creates the Custom Objects.
     *
     * @return array an array with custom objects {@link CustomTaskStoryVO} with their properties set to the values from the rows
     * and with additional data and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskStoryDAO();

    $taskStories = $dao->getByStoryId($this->storyId);

    return $this->TaskStoriesToCustomTaskStories($taskStories);

    }

}


/*//Test code;

$action= new GetStoryCustomTaskStoriesAction(3);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
