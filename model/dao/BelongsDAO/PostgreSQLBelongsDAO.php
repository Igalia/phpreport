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


/** File for PostgreSQLBelongsDAO
 *
 *  This file just contains {@link PostgreSQLBelongsDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/BelongsDAO.php');

/** DAO for relationship Belongs in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link BelongsDAO}.
 *
 * @see BelongsDAO
 */
class PostgreSQLBelongsDAO extends BelongsDAO{

    /** Belongs DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link BelongsDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see BelongsDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Value object constructor from edge A for PostgreSQL.
     *
     * This function creates a new {@link UserVO} with data retrieved from database edge A (User).
     *
     * @param array $row an array with the values from a row.
     * @return UserVO a {@link UserVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setAValues($row)
    {

    $userVO = new UserVO();

        $userVO->setId($row['id']);
        $userVO->setLogin($row['login']);
        $userVO->setPassword($row['password']);

    return $userVO;
    }

    /** Value object constructor from edge B for PostgreSQL.
     *
     * This function creates a new {@link UserGroupVO} with data retrieved from database edge B (User Group).
     *
     * @param array $row an array with the values from a row.
     * @return UserGroupVO a {@link UserGroupVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setBValues($row)
    {

    $userGroupVO = new UserGroupVO();

        $userGroupVO->setId($row['id']);
        $userGroupVO->setName($row['name']);

    return $userGroupVO;
    }

    /** Belongs entry retriever by id's for PostgreSQL.
     *
     * This function retrieves the row from Belongs table with the id's <var>$userId</var> and <var>$userGroupId</var>.
     *
     * @param int $userId the id (that matches with a User) of the row we want to retrieve.
     * @param int $userGroupId the id (that matches with a User Group) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link SQLQueryErrorException}
     */
    protected function getByIds($userId, $userGroupId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
    if (!is_numeric($userGroupId))
        throw new SQLIncorrectTypeException($userGroupId);
        $sql = "SELECT * FROM belongs WHERE usrid=" . $userId . " AND user_groupid=" . $userGroupId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** User Groups retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT user_group.* FROM belongs LEFT JOIN user_group ON belongs.user_groupid=user_group.id WHERE belongs.usrid=" . $userId . " ORDER BY user_group.id ASC";
    $result = $this->executeFromA($sql);
    return $result;
    }

    /** User Groups retriever by User login for PostgreSQL.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the login <var>$userLogin</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param string $userLogin the login of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserLogin($userLogin) {

    $sql = "SELECT user_group.* FROM belongs LEFT JOIN user_group ON belongs.user_groupid=user_group.id WHERE belongs.usrid= (SELECT id FROM usr WHERE login='" . $userLogin . "') ORDER BY user_group.id ASC";
    $result = $this->executeFromA($sql);
    return $result;

    }

    /** Users retriever by User Group id for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the id <var>$userGroupId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $userGroupId the id of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserGroupDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserGroupId($userGroupId) {
    if (!is_numeric($userGroupId))
        throw new SQLIncorrectTypeException($userGroupId);
        $sql = "SELECT usr.* FROM belongs LEFT JOIN usr ON belongs.usrid=usr.id WHERE belongs.user_groupid=" . $userGroupId . " ORDER BY usr.id ASC";
    $result = $this->executeFromB($sql);
    return $result;
    }

    /** Users retriever by User Group name for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship Belongs to the User Group with
     * the name <var>$userGroupName</var> and creates a {@link UserVO} with data from each row.
     *
     * @param string $userGroupName the name of the User Group whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserGroupDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserGroupName($userGroupName) {

        $sql = "SELECT usr.* FROM belongs LEFT JOIN usr ON belongs.usrid=usr.id WHERE belongs.user_groupid = (SELECT id FROM user_group WHERE name = '" . $userGroupName . "') ORDER BY usr.id ASC";
        $result = $this->executeFromB($sql);
        return $result;

    }

    /** Belongs relationship entry creator by User id and User Group id for PostgreSQL.
     *
     * This function creates a new entry in the table Belongs
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function create($userId, $userGroupId) {
    $affectedRows = 0;

    // Check for a belongs entry ID.
        $currBelongs = $this->getByIds($userId, $userGroupId);

        // If it doesn't exist, then create.
        if(sizeof($currBelongs) == 0) {
        $sql = "INSERT INTO belongs (usrid, user_groupid) VALUES (" . $userId . ", " . $userGroupId . ")";

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $affectedRows = pg_affected_rows($res);
    }

    return $affectedRows;

    }

    /** Belongs relationship entry deleter by User id and User Group id for PostgreSQL.
     *
     * This function deletes a entry in the table Belongs
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function delete($userId, $userGroupId) {
        $affectedRows = 0;

        // Check for a belongs entry ID.
        $currBelongs = $this->getByIds($userId, $userGroupId);

        // If it exists, then delete.
        if(sizeof($currBelongs) > 0) {
            $sql = "DELETE FROM belongs WHERE usrid=" . $userId . " AND user_groupid=" . $userGroupId;

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLBelongsDAO();

var_dump($dao->getByUserGroupName('staff'));

// We create a new entry

$userId = 1;

$userGroupId = 1;

$dao->create($userId, $userGroupId);

// We search for the new entry from side A

$userGroups = $dao->getByUserId($userId);

foreach ($userGroups as $group)
    print ("User group for user ". $userId ." : " . $group->getName() . "\n");

// We search for the new entry from side B

$users = $dao->getByUserGroupId($userGroupId);

foreach ($users as $user)
    print ("User for user group ". $userGroupId ." : " . $user->getLogin() . "\n");

// We delete the new entry

$dao->delete($userId, $userGroupId);*/
