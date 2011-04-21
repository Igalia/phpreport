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


/** File for PostgreSQLUserGroupDAO
 *
 *  This file just contains {@link PostgreSQLUserGroupDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserGroupDAO/UserGroupDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/PostgreSQLBelongsDAO.php');

/** DAO for User Groups in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link UserGroupDAO}.
 *
 * @see UserGroupDAO, UserGroupVO
 */
class PostgreSQLUserGroupDAO extends UserGroupDAO{

    /** User Group DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link UserGroupDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see UserGroupDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** User Group value object constructor for PostgreSQL.
     *
     * This function creates a new {@link UserGroupVO} with data retrieved from database.
     *
     * @param array $row an array with the User Group values from a row.
     * @return UserGroupVO a {@link UserGroupVO} with its properties set to the values from <var>$row</var>.
     * @see UserGroupVO
     */
    protected function setValues($row)
    {

    $userGroupVO = new UserGroupVO();

        $userGroupVO->setId($row[id]);
        $userGroupVO->setName($row[name]);

    return $userGroupVO;
    }

    /** User Group retriever by name for PostgreSQL.
     *
     * This function retrieves the row from User Group table with the name <var>$groupName</var> and creates a {@link UserGroupVO} with its data.
     *
     * @param string $groupName the login of the row we want to retrieve.
     * @return UserGroupVO a value object {@link UserGroupVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserGroupName($groupName) {
        $sql = "SELECT * FROM user_group WHERE name='".$groupName."'";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** User Group retriever by id for PostgreSQL.
     *
     * This function retrieves the row from User Group table with the id <var>$groupId</var> and creates a {@link UserGroupVO} with its data.
     *
     * @param int $groupId the id of the row we want to retrieve.
     * @return UserGroupVO a value object {@link UserGroupVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($groupId) {
        if (!is_numeric($groupId))
        throw new SQLIncorrectTypeException($groupId);
        $sql = "SELECT * FROM user_group WHERE id=".$groupId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Users retriever by User Group id for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the id <var>$userGroupId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getUsers($userGroupId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->getByUserGroupId($userGroupId);

    }

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
    public function getUsersByUserGroupName($userGroupName) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->getByUserGroupName($userGroupName);

    }

    /** Belongs relationship entry creator by User Group id and User id for PostgreSQL.
     *
     * This function creates a new entry in the table Belongs (that represents that relationship between User Groups and Users)
     * with the User Group id <var>$userGroupId</var> and the User id <var>$userId</var>.
     *
     * @param int $userGroupId the id of the User Group we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the User Group.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addUser($userGroupId, $userId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->create($userId, $userGroupId);

    }

    /** Belongs relationship entry deleter by User Group id and User id for PostgreSQL.
     *
     * This function deletes a entry in the table Belongs (that represents that relationship between User Groups and Users)
     * with the User Group id <var>$userGroupId</var> and the User id <var>$userId</var>.
     *
     * @param int $userGroupId the id of the User Group whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeUser($userGroupId, $userId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->delete($userId, $userGroupId);

    }

    /** User Groups retriever for PostgreSQL.
     *
     * This function retrieves all rows from User Group table and creates a {@link UserGroupVO} with data from each row.
     *
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM user_group";
        return $this->execute($sql);
    }

    /** User Group updater for PostgreSQL.
     *
     * This function updates the data of a User Group by its {@link UserGroupVO}.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(UserGroupVO $userGroupVO) {
    $affectedRows = 0;

        if($userGroupVO->getId() >= 0) {
            $currUserGroupVO = $this->getById($userGroupVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currUserGroupVO) > 0) {

            $sql = "UPDATE user_group SET name=" . DBPostgres::checkStringNull($userGroupVO->getName()) .  " WHERE id=".$userGroupVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_user_group_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** User Group creator for PostgreSQL.
     *
     * This function creates a new row for a User Group by its {@link UserGroupVO}. The internal id of <var>$userGroupVO</var> will be set after its creation.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(UserGroupVO $userGroupVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO user_group (name) VALUES(" . DBPostgres::checkStringNull($userGroupVO->getName()) .  ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_user_group_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $userGroupVO->setId(DBPostgres::getId($this->connect, "user_group_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** User Group deleter for PostgreSQL.
     *
     * This function deletes the data of a User Group by its {@link UserGroupVO}.
     *
     * @param UserGroupVO $userGroupVO the {@link UserGroupVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(UserGroupVO $userGroupVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($userGroupVO->getId() >= 0) {
            $currUserGroupVO = $this->getById($userGroupVO->getId());
        }

        // Otherwise delete a user.
        if(sizeof($currUserGroupVO) > 0) {
            $sql = "DELETE FROM user_group WHERE id=".$userGroupVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines inuserGroup order to do a simple test of the Dao



$dao = new PostgreSQLUserGroupDAO();

// We create a new User

$userGroup = new UserGroupVO();

$userGroup->setName("the others");

$dao->create($userGroup);

print ("New user group Id is ". $userGroup->getId() ."\n");

// We search for the new Id

$userGroup = $dao->getById($userGroup->getId());

print ("New user group Id found is ". $userGroup->getId() ."\n");

// We update the user with a differente login

$userGroup->setName("the unnamed ones");

$dao->update($userGroup);

// We search for the new login

$userGroup = $dao->getByUserGroupName("the unnamed ones");

print ("User group Id found is ". $userGroup->getId() ."\n");

// We delete the new user

$dao->delete($userGroup);*/
