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
abstract class ProjectDAO extends BaseDAO {

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

    /** Projects retriever.
     *
     * This function retrieves all rows from Project table and creates a {@link ProjectVO} with data from each row.
     *
     * @param null $userLogin optional parameter to list down only projects assigned to a user
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
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
     *        NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only trojects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     * @param long $customerId value to filter projects by their customer field.
     *        NULL to deactivate filtering by this field.
     * @param string $cname string to filter projects by their customer name. NULL
     *        to deactivate filtyering by this field
     * @param boolean $returnExtendedInfo flag to check if the response should include more information
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll($userLogin = NULL, $active = False, $orderField = 'id', $description = NULL,
        $filterStartDate = NULL, $filterEndDate = NULL, $activation = NULL, $areaId = NULL, $type = NULL,
        $customerId = NULL, $cname = NULL, $returnExtendedInfo = False);

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

    public abstract function getByDescription(string $description): ?int;

    /** Project updater.
     *
     * This function updates the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public abstract function update(ProjectVO $projectVO);

    /** Project creator.
     *
     * This function creates a new row for a Project by its {@link ProjectVO}.
     * The internal id of <var>$projectVO</var> will be set after its creation.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to insert on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     * @throws {@link OperationErrorException}
     */
    public abstract function create(ProjectVO $projectVO);

    /** Project deleter.
     *
     * This function deletes the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to delete from database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(ProjectVO $projectVO);

}
