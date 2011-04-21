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


/** File for ProjectDAO
 *
 *  This file just contains {@link ProjectDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Projects
 *
 *  This is the base class for all types of Project DAOs responsible for working with data from Project table, providing a common interface.
 *
 * @see DAOFactory::getProjectDAO(), ProjectVO
 */
abstract class ProjectDAO extends BaseDAO{

    /** Project DAO constructor.
     *
     * This is the base constructor of Project DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Project retriever by id.
     *
     * This function retrieves the row from Project table with the id <var>$projectId</var> and creates a {@link ProjectVO} with its data.
     *
     * @param int $projectId the id of the row we want to retrieve.
     * @return ProjectVO a value object {@link ProjectVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($projectId);

    /** Projects retriever by Area id.
     *
     * This function retrieves the rows from Project table that are associated with the Area with
     * the id <var>$areaId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Projects we want to retrieve.
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByAreaId($areaId, $orderField = 'id');

    /** Users retriever by Project id (relationship ProjectUser).
     *
     * This function retrieves the rows from User table that are assigned through relationship ProjectUser to the Project with
     * the id <var>$projectId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectUserDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getUsersProject($projectId);

    /** ProjectUser relationship entry creator by Project id and User id.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addUserProject($projectId, $userId);

    /** ProjectUser relationship entry deleter by Project id and User id.
     *
     * This function deletes a entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeUserProject($projectId, $userId);

    /** Users retriever by Project id (relationship Works).
     *
     * This function retrieves the rows from User table that are assigned through relationship Works to the Project with
     * the id <var>$projectId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see WorksDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getUsersWorks($projectId);

    /** Works relationship entry creator by Project id and User id.
     *
     * This function creates a new entry in the table Works (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addUserWorks($projectId, $userId);

    /** Works relationship entry deleter by Project id and User id.
     *
     * This function deletes a entry in the table Works (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeUserWorks($projectId, $userId);

    /** Customers retriever by Project id.
     *
     * This function retrieves the rows from Customer table that are assigned through relationship Requests to the Project with
     * the id <var>$projectId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see RequestsDAO, CustomerDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getCustomers($projectId);

    /** Iterations retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Iteration table that are assigned through relationship Plans to the Project with
     * the id <var>$projectId</var> and creates a {@link IterationVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Iterations we want to retrieve.
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see IterationDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getIterations($projectId);

    /** Modules retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Module table that are assigned through relationship Plans to the Project with
     * the id <var>$projectId</var> and creates a {@link ModuleVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Modules we want to retrieve.
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ModuleDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getModules($projectId);

    /** Requests relationship entry creator by Project id and Customer id.
     *
     * This function creates a new entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Project id <var>$projectId</var> and the Customer id <var>$customerId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the Customer.
     * @param int $customerId the id of the Customer we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, CustomerDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addCustomer($projectId, $customerId);

    /** Requests relationship entry deleter by Project id and Customer id.
     *
     * This function deletes a entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Project id <var>$projectId</var> and the Customer id <var>$customerId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the Customer we want to delete.
     * @param int $customerId the id of the Customer whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, CustomerDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeCustomer($projectId, $customerId);

    /** Tasks retriever by Project id.
     *
     * This function retrieves the rows from Task table that are assigned to the Project with
     * the id <var>$projectId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getTasks($projectId);

    /** Projects retriever.
     *
     * This function retrieves all rows from Project table and creates a {@link ProjectVO} with data from each row.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll($active = False, $orderField = 'id');

    /** Projects retriever.
     *
     * This function retrieves the rows from Project table, applying three optional conditions:
     * projects assigned to a specific Customer through Requests,
     * projects related with a User through ProjectUser
     * or projects with the Activation flag as True.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param string $userLogin login of the user we want to use as a filter.
     * @param bool $active parameter for obtaining only the active Projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByCustomerUserLogin($customerId = NULL, $userLogin = NULL, $active = False, $orderField = 'id');

    /** Custom Projects retriever.
     *
     * This function retrieves all rows from Project table and creates a {@link CustomProjectVO} with data from each row,
     * and additional ones.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomProjectVO} with their properties set to the values from the rows
     * and the additional data, and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getAllCustom($active = False, $orderField = 'id');

    /** Custom Projects retriever with filters.
     *
     * This function retrieves a subset of rows from Project table and creates a
     * {@link CustomProjectVO} with data from each row.
     *
     * Multiple fields can be used as filters; to disable a filter, a NULL value
     * has to be passed on that parameter.
     *
     * @param string $description string to filter projects by their description
     *        field. Projects with a description that contains this string will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterStartDate start date of the time filter for
     *        projects. Projects will a finish date later than this date will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterEndDate end date of the time filter for projects.
     *        Projects will a start date sooner than this date will be returned.
     *        NULL to deactivate filtering by this field.
     * @param boolean $activation filter projects by their activation field.
     *        NULL to deactivate filtering by this field.
     * @param long $areaId value to filter projects by their area field.
     *        projects. NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only trojects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     * @return array an array with value objects {@link CustomProjectVO} with
     *         their properties set to the values from the rows and the
     *         additional data.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getFilteredCustom($description = NULL,
            $filterStartDate = NULL, $filterEndDate = NULL, $activation = NULL,
            $areaId = NULL, $type = NULL);

    /** Project partial updater.
     *
     * This function updates only some fields of the data of a Project by its {@link ProjectVO}, reading
     * the flags on the associative array <var>$update</var>.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @param array $update an array with flags for updating or not the different fields.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function partialUpdate(ProjectVO $projectVO, $update);

    /** Project updater.
     *
     * This function updates the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function update(ProjectVO $projectVO);

    /** Project creator.
     *
     * This function creates a new row for a Project by its {@link ProjectVO}.
     * The internal id of <var>$projectVO</var> will be set after its creation.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function create(ProjectVO $projectVO);

    /** Project deleter.
     *
     * This function deletes the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(ProjectVO $projectVO);

}
