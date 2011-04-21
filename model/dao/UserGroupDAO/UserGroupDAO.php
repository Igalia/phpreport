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


/** File for UserGroupDAO
 *
 *  This file just contains {@link UserGroupDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for User Groups
 *
 *  This is the base class for all types of User Group DAOs responsible for working with data from User Group table, providing a common interface.
 *
 * @see DAOFactory::getUserGroupDAO(), UserGroupVO
 */
abstract class UserGroupDAO extends BaseDAO{

    /** User Group DAO constructor.
     *
     * This is the base constructor of User Group DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** User Group retriever by name.
     *
     * This function retrieves the row from User Group table with the name <var>$groupName</var> and creates a {@link UserGroupVO} with its data.
     *
     * @param string $groupName the login of the row we want to retrieve.
     * @return UserGroupVO a value object {@link UserGroupVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getByUserGroupName($groupName);

    /** User Group retriever by id.
     *
     * This function retrieves the row from User Group table with the id <var>$groupId</var> and creates a {@link UserGroupVO} with its data.
     *
     * @param int $groupId the id of the row we want to retrieve.
     * @return UserGroupVO a value object {@link UserGroupVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getById($groupId);

    /** Users retriever by User Group id.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the id <var>$userGroupId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getUsers($userGroupId);

    /** Users retriever by User Group name.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the name <var>$userGroupName</var> and creates a {@link UserVO} with data from each row.
     *
     * @param string $userGroupName the name of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getUsersByUserGroupName($userGroupName);

    /** Belongs relationship entry creator by User Group id and User id.
     *
     * This function creates a new entry in the table Belongs (that represents that relationship between User Groups and Users)
     * with the User Group id <var>$userGroupId</var> and the User id <var>$userId</var>.
     *
     * @param int $userGroupId the id of the User Group we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the User Group.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addUser($userGroupId, $userId);

    /** Belongs relationship entry deleter by User Group id and User id.
     *
     * This function deletes a entry in the table Belongs (that represents that relationship between User Groups and Users)
     * with the User Group id <var>$userGroupId</var> and the User id <var>$userId</var>.
     *
     * @param int $userGroupId the id of the User Group whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserDAO
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function removeUser($userGroupId, $userId);

    /** User Groups retriever.
     *
     * This function retrieves all rows from User Group table and creates a {@link UserGroupVO} with data from each row.
     *
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function getAll();

    /** User Group updater.
     *
     * This function updates the data of a User Group by its {@link UserGroupVO}.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(UserGroupVO $userGroupVO);

    /** User Group creator.
     *
     * This function creates a new row for a User Group by its {@link UserGroupVO}. The internal id of <var>$userGroupVO</var> will be set after its creation.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(UserGroupVO $userGroupVO);

    /** User Group deleter.
     *
     * This function deletes the data of a User Group by its {@link UserGroupVO}.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link InvalidOperationException}
     */
    public abstract function delete(UserGroupVO $userGroupVO);

}
