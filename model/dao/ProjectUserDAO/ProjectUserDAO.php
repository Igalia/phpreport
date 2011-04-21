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


/** File for ProjectUserDAO
 *
 *  This file just contains {@link ProjectUserDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/BaseRelationshipDAO.php');

/** DAO for relationship ProjectUser
 *
 *  This is the base class for all types of relationship ProjectUser DAOs responsible for working with data from tables related to that
 *  relationship (User, Project and ProjectUser), providing a common interface. <br><br>Its edges are:
 * - A: User
 * - B: Project
 *
 * @see DAOFactory::getProjectUserDAO(), UserDAO, UserGroupDAO, UserVO, UserGroupVO
 */
abstract class ProjectUserDAO extends BaseRelationshipDAO{

    /** ProjectUser DAO constructor.
     *
     * This is the base constructor of ProjectUser DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** ProjectUser entry retriever by id's.
     *
     * This function retrieves the row from ProjectUser table with the id's <var>$userId</var> and <var>$projectId</var>.
     *
     * @param int $userId the id (that matches with a User) of the row we want to retrieve.
     * @param int $projectId the id (that matches with a Project) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link OperationErrorException}
     */
    protected abstract function getByIds($userId, $projectId);

    /** Projects retriever by User id.
     *
     * This function retrieves the rows from Project table that are assigned through relationship ProjectUser to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserId($userId);

    /** Users retriever by Project id.
     *
     * This function retrieves the rows from User table that are assigned through relationship ProjectUser to the Project with
     * the id <var>$projectId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByProjectId($projectId);

    /** ProjectUser relationship entry creator by User id and Project id.
     *
     * This function creates a new entry in the table ProjectUser
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function create($userId, $projectId);

    /** ProjectUser relationship entry deleter by User id and Project id.
     *
     * This function deletes a entry in the table ProjectUser
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function delete($userId, $projectId);

}
