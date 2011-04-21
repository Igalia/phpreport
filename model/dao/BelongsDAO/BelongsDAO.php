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


/** File for BelongsDAO
 *
 *  This file just contains {@link BelongsDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/BaseRelationshipDAO.php');

/** DAO for relationship Belongs
 *
 *  This is the base class for all types of relationship Belongs DAOs responsible for working with data from tables related to that relationship
 *  (User, User Group and Belongs), providing a common interface. <br><br>Its edges are:
 * - A: User
 * - B: User Group
 *
 * @see DAOFactory::getBelongsDAO(), UserDAO, UserGroupDAO, UserVO, UserGroupVO
 */
abstract class BelongsDAO extends BaseRelationshipDAO{

    /** Belongs DAO constructor.
     *
     * This is the base constructor of Belongs DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Belongs entry retriever by id's.
     *
     * This function retrieves the row from Belongs table with the id's <var>$userId</var> and <var>$userGroupId</var>.
     *
     * @param int $userId the id (that matches with a User) of the row we want to retrieve.
     * @param int $userGroupId the id (that matches with a User Group) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    protected abstract function getByIds($userId, $userGroupId);

    /** User Groups retriever by User id.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getByUserId($userId);

    /** User Groups retriever by User login for PostgreSQL.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the login <var>$userLogin</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param string $userLogin the login of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, UserGroupDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserLogin($login);

    /** Users retriever by User Group id.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the id <var>$userGroupId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserGroupDAO, UserDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getByUserGroupId($userGroupId);

    /** Users retriever by User Group name for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the name <var>$userGroupName</var> and creates a {@link UserVO} with data from each row.
     *
     * @param string $userGroupName the name of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserGroupDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserGroupName($userGroupName);

    /** Belongs relationship entry creator by User id and User Group id.
     *
     * This function creates a new entry in the table Belongs
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function create($userId, $userGroupId);

    /** Belongs relationship entry deleter by User id and User Group id.
     *
     * This function deletes a entry in the table Belongs
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, UserGroupDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function delete($userId, $userGroupId);

}
