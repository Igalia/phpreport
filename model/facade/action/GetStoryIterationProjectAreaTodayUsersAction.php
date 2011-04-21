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


/** File for GetStoryIterationProjectAreaTodayUsersAction
 *
 *  This file just contains {@link GetStoryIterationProjectAreaTodayUsersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get Story Iteration Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Story Iteration Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetStoryIterationProjectAreaTodayUsersAction extends Action{

    /** The Story Id
     *
     * @param int $storyId the id of the Project Story whose related Users (through Area) we want to retrieve.
     *
     * @var int
     */
    private $storyId;

    /** GetStoryProjectAreaTodayUsersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Project Story whose related Users (through Area) we want to retrieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_STORY_ITERATION_PROJECT_AREA_TODAY_USERS_PREACTION";
        $this->postActionParameter="GET_STORY_ITERATION_PROJECT_AREA_TODAY_USERS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Users from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getByStoryIterationProjectAreaToday($this->storyId);

    }

}


/*//Test code;

$action= new GetStoryIterationProjectAreaTodayUsersAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
