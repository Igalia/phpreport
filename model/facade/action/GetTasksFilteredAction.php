<?php
/*
 * Copyright (C) 2010-2012 Igalia, S.L. <info@igalia.com>
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


/** File for GetTasksFilteredAction
 *
 *  This file just contains {@link GetTasksFilteredAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Get Tasks Filtered Action
 *
 *  This action is used for retrieving tasks filtered by multiple fields.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class GetTasksFilteredAction extends Action{

    private $filterStartDate;
    private $filterEndDate;
    private $telework;
    private $onsite;
    private $filterText;
    private $type;
    private $userId;
    private $projectId;
    private $customerId;
    private $taskStoryId;
    private $filterStory;
    private $emptyText;
    private $emptyStory;

    /** GetTasksFilteredAction constructor.
     *
     * This is just the constructor of this action.
     *
     * Multiple fields can be used as filters; to disable a filter, a NULL value
     * has to be passed on that parameter.
     *
     * @param DateTime $filterStartDate start date to filter tasks. Those tasks
     *        having a date equal or later than this one will be returned. NULL
     *        to deactivate filtering by this field.
     * @param DateTime $filterEndDate end date to filter tasks. Those tasks
     *        having a date equal or sooner than this one will be returned. NULL
     *        to deactivate filtering by this field.
     * @param boolean $telework filter tasks by their telework field.
     *        NULL to deactivate filtering by this field.
     * @param boolean $onsite filter tasks by their onsite field.
     *        NULL to deactivate filtering by this field.
     * @param string $filterText string to filter tasks by their description
     *        field. Tasks with a description that contains this string will
     *        be returned. NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only projects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     * @param int $userId id of the user whose tasks will be filtered. NULL to
     *        deactivate filtering by this field.
     * @param int $projectId id of the project which tasks will be filtered by.
     *        NULL to deactivate filtering by this field.
     * @param int $customerId id of the customer whose tasks will be filtered.
     *        NULL to deactivate filtering by this field.
     * @param int $taskStoryId id of the story inside the XP tracker which tasks
     *        will be filtered. NULL to deactivate filtering by this field.
     * @param string $filterStory string to filter tasks by their story field.
     *        Tasks with a story that contains this string will be returned.
     *        NULL to deactivate filtering by this field.
     * @param boolean $emptyText filter tasks by the presence, or absence, of
     *        text in the description field. NULL to deactivate this field; if
     *        not NULL, the parameter $filterText will be ignored.
     * @param boolean $emptyStory filter tasks by the presence, or absence, of
     *        text in the story field. NULL to deactivate this field; if
     *        not NULL, the parameter $filterStory will be ignored.
     * @return array an array with value objects {@link TaskVO} with their
     *         properties set to the values from the rows and ordered
     *         ascendantly by their database internal identifier.
     */
    public function __construct($filterStartDate = NULL, $filterEndDate = NULL,
            $telework = NULL, $onsite = NULL, $filterText = NULL, $type = NULL, $userId = NULL,
            $projectId = NULL, $customerId = NULL, $taskStoryId = NULL,
            $filterStory = NULL, $emptyText = NULL, $emptyStory = NULL) {
        $this->filterStartDate = $filterStartDate;
        $this->filterEndDate = $filterEndDate;
        $this->telework = $telework;
        $this->onsite = $onsite;
        $this->filterText = $filterText;
        $this->type = $type;
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->customerId = $customerId;
        $this->taskStoryId = $taskStoryId;
        $this->filterStory = $filterStory;
        $this->emptyText = $emptyText;
        $this->emptyStory = $emptyStory;

        $this->preActionParameter = "GET_TASKS_FILTERED_PREACTION";
        $this->postActionParameter = "GET_TASKS_FILTERED_POSTACTION";
    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Tasks.
     *
     * @return array an array with value objects {@link TaskVO} with their
     *         properties set to the values from the rows and ordered
     *         ascendantly by their database internal identifier.
     */
    protected function doExecute() {
        $dao = DAOFactory::getTaskDAO();

        return $dao->getFiltered($this->filterStartDate, $this->filterEndDate,
                $this->telework, $this->onsite, $this->filterText, $this->type,
                $this->userId, $this->projectId, $this->customerId, $this->taskStoryId,
                $this->filterStory, $this->emptyText, $this->emptyStory);
    }
}
