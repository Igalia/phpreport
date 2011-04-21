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


/** File for GetIterationCustomStoriesAction
 *
 *  This file just contains {@link GetIterationCustomStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryCustomTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskStoryVO.php');


/** Get Iteration Custom Stories Action
 *
 *  This action is used for retrieving all Custom Stories (Stories with additional data) related to an Iteration.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetIterationCustomStoriesAction extends GetStoryCustomTaskStoriesAction{

    /** The Iteration Id
     *
     * This variable contains the id of the Iteration whose Custom Stories we want to retieve.
     *
     * @var int
     */
    private $iterationId;

    /** GetIterationCustomStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $iterationId the id of the Iteration whose Custom Stories we want to retieve.
     */
    public function __construct($iterationId) {
        $this->iterationId=$iterationId;
        $this->preActionParameter="GET_ITERATION_CUSTOM_STORIES_PREACTION";
        $this->postActionParameter="GET_ITERATION_CUSTOM_STORIES_POSTACTION";

    }

    /** StoriesToCustomStories function.
     *
     * This function receives an array of value objects {@link StoryVO} and creates their custom objects {@link CustomStoryVO}.
     *
     * @param array $stories an array of value objects {@link StoryVO}.
     * @return array an array with custom objects {@link CustomStoryVO} with their properties set to the values from the value
     * objects {@link StoryVO} and with additional data and ordered ascendantly by their database internal identifier
     */
    protected function StoriesToCustomStories($stories) {

    $customStories = array();

    foreach ((array) $stories as $story)
    {

        $customStory = new CustomStoryVO();

        $customStory->setName($story->getName());

        $customStory->setId($story->getId());

        $customStory->setAccepted($story->getAccepted());

        $customStory->setStoryId($story->getStoryId());

        $customStory->setIterationId($story->getIterationId());

        $spent = 0.0;

        $toDo = 0.0;

        $estHours = 0.0;

        $developers = array();

        $dao = DAOFactory::getTaskStoryDAO();

        $taskStories = $dao->getByStoryId($customStory->getId());

        $customTaskStories = $this->TaskStoriesToCustomTaskStories($taskStories);

        if (!is_null($story->getUserId()))
        {

            $action = new getUserAction($story->getUserId());

            $customStory->setReviewer($action->execute());

        }

        foreach($customTaskStories as $taskStory)
        {

            $spent += $taskStory->getSpent();
            $toDo += $taskStory->getToDo();
            $estHours += $taskStory->getEstHours();
            $developer = $taskStory->getDeveloper();
            if (!is_null($developer))
                $developers[$developer->getLogin()] = $developer;

        }

        $customStory->setSpent($spent);

        $customStory->setEstHours($estHours);

        $customStory->setToDo($toDo);

        $customStory->setDevelopers($developers);

        $customStory->setDone($spent/($spent+$toDo));

        if ($estHours)
            $customStory->setOverrun((($spent+$toDo)/$estHours) - 1.0);

        $customStories[] = $customStory;

    }

    return $customStories;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Stories from persistent storing.
     *
     * @return array an array with custom objects {@link CustomStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();

    $stories = $dao->getByIterationId($this->iterationId);

    return $this->StoriesToCustomStories($stories);

    }

}


/*//Test code;

$action= new GetIterationCustomStoriesAction(8);
var_dump($action);
$result = $action->execute();
var_dump($result);*/
