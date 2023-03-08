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


/** File for PostgreSQLUserDAO
 *
 *  This file just contains {@link PostgreSQLUserDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/IncorrectLoginException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/UserDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectUserDAO/PostgreSQLProjectUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/PostgreSQLBelongsDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/PostgreSQLTaskDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ExtraHourDAO/PostgreSQLExtraHourDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaHistoryDAO/PostgreSQLAreaHistoryDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CityHistoryDAO/PostgreSQLCityHistoryDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/JourneyHistoryDAO/PostgreSQLJourneyHistoryDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/HourCostHistoryDAO/PostgreSQLHourCostHistoryDAO.php');


/** DAO for Users in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link UserDAO}.<br/>Password is stored in DB by its hash obtained with MD5.
 *
 * @see UserDAO, UserVO
 */
class PostgreSQLUserDAO extends UserDAO{

    /** User DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link UserDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see UserDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** User value object constructor for PostgreSQL.
     *
     * This function creates a new {@link UserVO} with data retrieved from database.
     *
     * @param array $row an array with the User values from a row.
     * @return UserVO a {@link UserVO} with its properties set to the values from <var>$row</var>.
     * @see UserVO
     */
    protected function setValues($row)
    {

    $userVO = new UserVO();

        $userVO->setId($row['id']);
        $userVO->setLogin($row['login']);
        $userVO->setPassword($row['password']);
    $userVO->setGroups((array) $this->getGroupsByLogin($userVO->getLogin()));

    return $userVO;
    }

    /** Login for PostgreSQL
     *
     * This function makes login for a user, checking if provided login and password match.
     *
     * @param string $login the login of the user.
     * @param string $password the password of the user.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row of the logged user.
     * @throws {@link IncorrectLoginException}
     */
    public function login($userLogin, $userPassword) {

        $sql = "SELECT * FROM usr WHERE login=" . DBPostgres::checkStringNull($userLogin) . " AND password";

        if (DBPostgres::checkStringNull($userPassword) == "NULL")
            $sql = $sql  . " is NULL";
        else
            $sql = $sql . "=md5(" . DBPostgres::checkStringNull($userPassword) . ")";

        $result = $this->execute($sql);

        if (isset($result[0]))
        {
            // We normally won't want to retrieve md5 password
            $result[0]->setPassword($userPassword);
            return $result[0];
        }

        throw new IncorrectLoginException();

    }

    /** User retriever by id for PostgreSQL.
     *
     * This function retrieves the row from User table with the id <var>$userId</var> and creates a {@link UserVO} with its data.
     *
     * @param int $userId the id of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($userId) {
        if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM usr WHERE id=". (int) $userId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** User retriever by login for PostgreSQL.
     *
     * This function retrieves the row from User table with the login <var>$userLogin</var> and creates a {@link UserVO} with its data.
     *
     * @param string $userLogin the login of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row, or NULL in case the user doesn't exist.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserLogin($userLogin) {
        $sql = "SELECT * FROM usr WHERE login=" . DBPostgres::checkStringNull($userLogin);
        $result = $this->execute($sql);
        if(count($result) == 0)
            return NULL;

        return $result[0];
    }

    /** Users retriever for PostgreSQL.
     *
     * This function retrieves all rows from User table and creates a {@link UserVO} with data from each row.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM usr ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Projects retriever by id (relationship ProjectUser) for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are assigned through relationship ProjectUser to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getProjectsUser($userId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->getByUserId($userId);

    }

    /** ProjectUser relationship entry creator by User id and Project id for PostgreSQL.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorDBPostgres::checkStringNull($userVO->getPassword()) == "NULL"Exception}
     */
    public function addProjectUser($userId, $projectId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->create($userId, $projectId);

    }

    /** ProjectUser relationship entry deleter by User id and Project id for PostgreSQL.
     *
     * This function deletes a entry in the table ProjectUser (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeProjectUser($userId, $projectId) {

    $dao = DAOFactory::getProjectUserDAO();
    return $dao->delete($userId, $projectId);

    }

    /** User Groups retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getGroups($userId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->getByUserId($userId);

    }

    /** User Groups retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getGroupsByLogin($userLogin) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->getByUserLogin($userLogin);

    }

    /** Belongs relationship entry creator by User id and User Group id for PostgreSQL.
     *
     * This function creates a new entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addGroup($userId, $groupId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->create($userId, $groupId);

    }

    /** Belongs relationship entry deleter by User id and Project id for PostgreSQL.
     *
     * This function deletes a entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeGroup($userId, $groupId) {

    $dao = DAOFactory::getBelongsDAO();
    return $dao->delete($userId, $groupId);

    }

    /** Extra Hours retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from Extra Hour table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link ExtraHourVO} with data from each row.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ExtraHourDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getExtraHours($userId) {

    $dao = DAOFactory::getExtraHourDAO();
    return $dao->getByUserId($userId);

    }

    /** Area History retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from Area History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link AreaHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Area History we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see AreaHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getAreaHistory($userId) {

    $dao = DAOFactory::getAreaHistoryDAO();
    return $dao->getByUserId($userId);

    }

    /** User retriever by Area and date for PostgreSQL.
     *
     * This function retrieves the row from User table assigned to an Area on a date.
     *
     * @param int $areaId the id of the area whose Users we want to retrieve.
     * @param DateTime $date the date whose history we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByAreaDate($areaId, DateTime $date) {
        if (!is_numeric($areaId))
        throw new SQLIncorrectTypeException($areaId);
        $sql = "SELECT * FROM usr WHERE id IN (SELECT usrid FROM area_history WHERE ((init_date <= " . DBPostgres::formatDate($date) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($date) . " OR end_date IS NULL) AND areaid = " . $areaId  . "))";
        $result = $this->execute($sql);
        return $result;
    }

    /** Hour Cost History retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link HourCostHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see HourCostHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getHourCostHistory($userId) {

    $dao = DAOFactory::getHourCostHistoryDAO();
    return $dao->getByUserId($userId);

    }

    /** City History retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from City History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CityHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose City History we want to retrieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CityHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCityHistory($userId) {

    $dao = DAOFactory::getCityHistoryDAO();
    return $dao->getByUserId($userId);

    }

    /** Journey History retriever by id for PostgreSQL.
     *
     * This function retrieves the rows from Journey History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link JourneyHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Journey History we want to retrieve.
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see JourneyHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getJourneyHistory($userId) {

    $dao = DAOFactory::getJourneyHistoryDAO();
    return $dao->getByUserId($userId);

    }

    /** User updater for PostgreSQL.
     *
     * This function updates the data of a User by its {@link UserVO}. If the
     * UserVO doesn't contain a password, it won't be updated.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(UserVO $userVO) {

        $affectedRows = 0;

        if($userVO->getId() >= 0) {
            $currUserVO = $this->getById($userVO->getId());
        }

        // If the query returned a row then update
        if(isset($currUserVO)) {

            $sql = "UPDATE usr SET login=" . DBPostgres::checkStringNull($userVO->getLogin());

            if (DBPostgres::checkStringNull($userVO->getPassword()) != "NULL")
                $sql .= ", password=md5(" . DBPostgres::checkStringNull($userVO->getPassword()) . ")";

            $sql .= " WHERE id=" .$userVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_usr_login"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** User creator for PostgreSQL.
     *
     * This function creates a new row for a User by its {@link UserVO}.
     * The internal id of <var>$userVO</var> will be set after its creation.
     *projectId
     * @param UserVO $userVO the {@link UserVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(UserVO $userVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO usr (login, password) VALUES(" . DBPostgres::checkStringNull($userVO->getLogin()) . ", ";
        if (DBPostgres::checkStringNull($userVO->getPassword()) == "NULL")
            $sql = $sql . "NULL)";
        else
            $sql = $sql . "md5(" . DBPostgres::checkStringNull($userVO->getPassword()) . "))";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_usr_login"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $userVO->setID(DBPostgres::getId($this->connect, "usr_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** User deleter for PostgreSQL.
     *
     * This function deletes the data of a User by its {@link UserVO}.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(UserVO $userVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($userVO->getId() >= 0) {
            $currUserVO = $this->getById($userVO->getId());
        }

        // Otherwise delete a user.
        if(isset($currUserVO)) {
            $sql = "DELETE FROM usr WHERE id=".$userVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLUserDAO();

// We create a new User

$user = new UserVO();
$user->setLogin("george");

$dao->create($user);

print ("New user Id is ". $user->getId() ."\n");

// We search for the new Id

$user = $dao->getById($user->getId());

print ("New user Id found is ". $user->getId() ."\n");

// We update the user with a differente login

$user->setLogin("paul");

$dao->update($user);

// We search for the new login

$user = $dao->getByUserLogin("paul");

print ("User Id found is ". $user->getId() ."\n");
print ("User Login found is ". $user->getLogin() ."\n");


// We add it to two groups

$dao->addGroup($user->getId(), 1);
$dao->addGroup($user->getId(), 2);

$groups = $dao->getGroups($user->getId());

foreach ($groups as $group)
    print ("Group name found is ". $group->getName() ."\n");

// We remove it from one group

$dao->removeGroup($user->getId(), 1);

$user = $dao->getByUserLogin("paul");

$groups = $user->getGroups();

foreach ($groups as $group)
    print ("Group name found now is ". $group->getName() ."\n");

// We delete the new user

$dao->removeGroup($user->getId(), 2);

$dao->delete($user);*/
