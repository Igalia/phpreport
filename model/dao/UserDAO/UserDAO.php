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


/** File for UserDAO
 *
 *  This file just contains {@link UserDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');
include_once(PHPREPORT_ROOT . '/util/IncorrectLoginException.php');

/** DAO for Users
 *
 *  This is the base class for all types of User DAOs responsible for working with data from User table, providing a common interface.
 *
 * @see DAOFactory::getUserDAO(), UserVO
 */
abstract class UserDAO extends BaseDAO{

    /** User DAO constructor.
     *
     * This is the base constructor of User DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Login
     *
     * This function makes login for a user, checking if provided login and password match.
     *
     * @param string $login the login of the user.
     * @param string $password the password of the user.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row of the logged user.
     * @throws {@link IncorrectPasswordException}, {@link OperationErrorException}
     */
    public abstract function login($login, $password);

    /** User retriever by id.
     *
     * This function retrieves the row from User table with the id <var>$userId</var> and creates a {@link UserVO} with its data.
     *
     * @param int $userId the id of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}, {@link IncorrectTypeException}
     */
    public abstract function getById($userId);

    /** User retriever by login.
     *
     * This function retrieves the row from User table with the login <var>$userLogin</var> and creates a {@link UserVO} with its data.
     *
     * @param string $userLogin the login of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserLogin($userLogin);

    /** User retriever by Area and date.
     *
     * This function retrieves the row from User table assigned to an Area on a date.
     *
     * @param int $areaId the id of the area whose Users we want to retrieve.
     * @param DateTime $date the date whose history we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByAreaDate($areaId, DateTime $date);

    /** User retriever by Iteration Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Iteration with id <var>$projectid</var> today.
     *
     * @param int $iterationid the id of the Project Iteration whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByIterationProjectAreaToday($iterationid);

    /** User retriever by Module Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Module with id <var>$moduleid</var> today.
     *
     * @param int $moduleid the id of the Project Module whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByModuleProjectAreaToday($moduleid);

    /** User retriever by Story Iteration Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Iteration Story with id <var>$storyid</var> today.
     *
     * @param int $storyid the id of the Project Iteration Story whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByStoryIterationProjectAreaToday($storyid);

    /** User retriever by Section Module Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Module Section with id <var>$sectionid</var> today.
     *
     * @param int $sectionid the id of the Project Module Section whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getBySectionModuleProjectAreaToday($sectionid);

    /** Users retriever.
     *
     * This function retrieves all rows from User table and creates a {@link UserVO} with data from each row.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Projects retriever by id (relationship ProjectUser).
     *
     * This function retrieves the rows from Project table that are assigned through relationship ProjectUser to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getProjectsUser($userId);

    /** ProjectUser relationship entry creator by User id and Project id.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addProjectUser($userId, $projectId);

    /** ProjectUser relationship entry deleter by User id and Project id.
     *
     * This function deletes a entry in the table ProjectUser (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeProjectUser($userId, $projectId);

    /** Projects retriever by id (relationship Works).
     *
     * This function retrieves the rows from Project table that are assigned through relationship Works to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see WorksDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getProjectsWorks($userId);

    /** Works relationship entry creator by User id and Project id.
     *
     * This function creates a new entry in the table Works (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addProjectWorks($userId, $projectId);

    /** Works relationship entry deleter by User id and Project id.
     *
     * This function deletes a entry in the table Works (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeProjectWorks($userId, $projectId);

    /** User Groups retriever by id.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getGroups($userId);

    /** User Groups retriever by login for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from User Group that are assigned through relationship Belongs to the User with
     * the login <var>$userLogin</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userLogin the login of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getGroupsByLogin($userLogin);

    /** Belongs relationship entry creator by User id and User Group id.
     *
     * This function creates a new entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function addGroup($userId, $groupId);

    /** Belongs relationship entry deleter by User id and User Group id.
     *
     * This function deletes a entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function removeGroup($userId, $groupId);

    /** Tasks retriever by id.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getTasks($userId);

    /** Extra Hours retriever by id.
     *
     * This function retrieves the rows from Extra Hour table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link ExtraHourVO} with data from each row.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ExtraHourDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getExtraHours($userId);

    /** Custom Events retriever by id.
     *
     * This function retrieves the rows from Custom Event table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CustomEventVO} with data from each row.
     *
     * @param int $userId the id of the User whose Custom Events we want to retrieve.
     * @return array an array with value objects {@link CustomEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomEventDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getCustomEvents($userId);

    /** Area History retriever by id.
     *
     * This function retrieves the rows from Area History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link AreaHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Area History we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see AreaHistoryDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getAreaHistory($userId);

    /** Hour Cost History retriever by id.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link HourCostHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see HourCostHistoryDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getHourCostHistory($userId);

    /** City History retriever by id.
     *
     * This function retrieves the rows from City History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CityHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose City History we want to retrieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CityHistoryDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getCityHistory($userId);

    /** Journey History retriever by id.
     *
     * This function retrieves the rows from Journey History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link JourneyHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Journey History we want to retrieve.
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see JourneyHistoryDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getJourneyHistory($userId);

    /** User updater.
     *
     * This function updates the data of a User by its {@link UserVO}.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(UserVO $userVO);

    /** User creator.
     *
     * This function creates a new row for a User by its {@link UserVO}.
     * The internal id of <var>$userVO</var> will be set after its creation.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(UserVO $userVO);

    /** User deleter.
     *
     * This function deletes the data of a User by its {@link UserVO}.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function delete(UserVO $userVO);

}
