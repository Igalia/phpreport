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


/** File for LDAP/PostgreSQL HybridUserDAO
 *
 *  This file just contains {@link LDAP/PostgreSQL HybridUserDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/UserDAO.php');
include_once(PHPREPORT_ROOT . '/util/LDAPConnectionErrorException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPInvalidOperationException.php');
include_once(PHPREPORT_ROOT . '/util/LDAPOperationErrorException.php');
include_once(PHPREPORT_ROOT . '/util/IncorrectLoginException.php');


/** DAO for Users in LDAP/PostgreSQL Hybrid
 *
 *  This is the implementation for LDAP/PostgreSQL Hybrid of {@link UserDAO}.
 *
 * @see UserDAO, UserVO
 */
class HybridUserDAO extends UserDAO{

    protected $ldapConnect;

    /** User DAO for LDAP/PostgreSQL Hybrid constructor.
     *
     * This is the constructor of the implementation for LDAP/PostgreSQL Hybrid of {@link UserDAO}, and it calls its parent's constructor and creates a LDAP connection.
     *
     * @throws {@link DBConnectionErrorException}
     * @see UserDAO::__construct()
     */
    function __construct() {

        parent::__construct();

        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_SERVER');
        $parameters[] = ConfigurationParametersManager::getParameter('LDAP_PORT');

        $this->ldapConnect = ldap_connect($parameters[0], $parameters[1]);
        if ($this->ldapConnect == NULL) throw new LDAPConnectionErrorException("Server:" . $parameters[0] . " | Port:" . $parameters[1]);

    }

    /** User value object constructor for LDAP/PostgreSQL Hybrid.
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

    /** Login for LDAP/PostgreSQL Hybrid.
     *
     * This function makes login for a user, checking if provided login and password match.
     * The user must exist both in LDAP and DB to be valid.
     *
     * @param string $login the login of the user.
     * @param string $password the password of the user.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row of the logged user.
     * @throws {@link IncorrectLoginException}, {@link SQLQueryErrorException}
     */
    public function login($login, $password) {

        if (!$bn=@ldap_bind($this->ldapConnect,"uid=$login,ou=People," . ConfigurationParametersManager::getParameter('LDAP_BASE'),$password))
            throw new IncorrectLoginException("login - " . $login . " | password - " . $password);

        $sql = "SELECT * FROM usr WHERE login='". $login . "'";
        $result = $this->execute($sql);
        if(!isset($result[0])) {
            throw new IncorrectLoginException("login - " . $login . " | password - " . $password);
        }

        return $this->getByUserLogin($login);

    }

    /** User retriever by id for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the row from User table with the id <var>$userId</var> and creates a {@link UserVO} with its data.
     *
     * @param int $userId the id of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}, {@link SQLIncorrectTypeException}
     */
    public function getById($userId) {
        if (!is_numeric($userId))
            throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM usr WHERE id=". (int) $userId;
        $result = $this->execute($sql);
        return $result[0];
    }

    /** User retriever by login for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the row from User (in LDAP) with the login <var>$userLogin</var> and creates a {@link UserVO} with its data.
     *
     * @param string $userLogin the login of the row we want to retrieve.
     * @return UserVO a value object {@link UserVO} with its properties set to the values from the row, or NULL in case the user doesn't exist.
     * @throws {@link LDAPOperationErrorException}
     */
    public function getByUserLogin($userLogin) {

        $groups = $this->getGroupsByLogin($userLogin);

        if (is_null($groups))
            return NULL;

        $user = new UserVO();

        $sql = "SELECT * FROM usr WHERE login='". $userLogin . "'";
        $result = $this->execute($sql);

        if(count($result) == 0)
            //a user with that login could not be found, return null
            return NULL;

        $user->setLogin($userLogin);
        $user->setGroups($groups);
        $user->setId($result[0]->getId());

        return $user;

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
        $sql = "SELECT * FROM usr WHERE id IN (SELECT usrid FROM area_history WHERE ((init_date <= " .
            DBPostgres::formatDate($date) . " OR init_date IS NULL) AND (end_date >= " .
            DBPostgres::formatDate($date) . " OR end_date IS NULL) AND areaid = " . $areaId  . "))";
        $result = $this->execute($sql);
        return $result;
    }

    /** User retriever by Iteration Project Area for PostgreSQL.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Iteration with id <var>$projectid</var> today.
     *
     * @param int $iterationid the id of the Project Iteration whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByIterationProjectAreaToday($iterationid) {
        if (!is_numeric($iterationid))
            throw new SQLIncorrectTypeException($iterationid);
        $sql = "SELECT usr.* FROM usr LEFT JOIN area_history on usr.id=usrid " .
            "WHERE (((current_date > init_date) AND ((current_date < end_date) OR (end_date IS NULL))) " .
            "AND areaid = (SELECT areaid FROM project LEFT JOIN iteration ON project.id=projectid WHERE iteration.id=" . $iterationid . "))";
        $result = $this->execute($sql);
        return $result;
    }

    /** User retriever by Module Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Module with id <var>$moduleid</var> today.
     *
     * @param int $moduleid the id of the Project Module whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByModuleProjectAreaToday($moduleid) {
        if (!is_numeric($moduleid))
            throw new SQLIncorrectTypeException($moduleid);
        $sql = "SELECT usr.* FROM usr LEFT JOIN area_history on usr.id=usrid " .
            "WHERE (((current_date > init_date) AND ((current_date < end_date) OR (end_date IS NULL))) " .
            "AND areaid = (SELECT areaid FROM project LEFT JOIN module ON project.id=projectid WHERE module.id=" . $moduleid . "))";
        $result = $this->execute($sql);
        return $result;
    }

    /** User retriever by Story Iteration Project Area for PostgreSQL.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Iteration Story with id <var>$storyid</var> today.
     *
     * @param int $storyid the id of the Project Iteration Story whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByStoryIterationProjectAreaToday($storyid) {
        if (!is_numeric($storyid))
            throw new SQLIncorrectTypeException($storyid);
        $sql = "SELECT usr.* FROM usr LEFT JOIN area_history on usr.id=usrid " .
            "WHERE (((current_date > init_date) AND ((current_date < end_date) OR (end_date IS NULL))) " .
            "AND areaid = (SELECT areaid FROM project LEFT JOIN iteration ON project.id=projectid " .
                "WHERE iteration.id=(SELECT iterationid FROM story where id=" . $storyid . ")))";
        $result = $this->execute($sql);
        return $result;
    }

    /** User retriever by Section Module Project Area.
     *
     * This function retrieves the row from User table assigned to the same Area as a Project Module Section with id <var>$sectionid</var> today.
     *
     * @param int $sectionid the id of the Project Module Section whose related Users (through Area) we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getBySectionModuleProjectAreaToday($sectionid) {
        if (!is_numeric($sectionid))
            throw new SQLIncorrectTypeException($sectionid);
        $sql = "SELECT usr.* FROM usr LEFT JOIN area_history on usr.id=usrid " .
            "WHERE (((current_date > init_date) AND ((current_date < end_date) OR (end_date IS NULL))) " .
            "AND areaid = (SELECT areaid FROM project LEFT JOIN module ON project.id=projectid " .
                "WHERE module.id=(SELECT moduleid FROM section where id=" . $sectionid . ")))";
        $result = $this->execute($sql);
        return $result;
    }

    /** Users retriever for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves all rows from User table and creates a {@link UserVO} with data from each row.
     * If a user exists in LDAP but isn't present in DB, it won't be listed.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}, {@link LDAPOperationErrorException}
     */
    public function getAll() {

        $dao = DAOFactory::getBelongsDAO();

        $usersLDAP = $dao->getByUserGroupName(ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));

        $sql = "SELECT * FROM usr ORDER BY id ASC";

        $usersDB = $this->execute($sql);

        foreach((array) $usersLDAP as $index => $userLDAP) {
            foreach((array) $usersDB as $userDB)
                if ($userLDAP->getLogin() == $userDB->getLogin())
                    $userLDAP->setId($userDB->getId());
            if($userLDAP->getId() == null)
                //the user doesn't exist in DB, delete it
                unset($usersLDAP[$index]);
        }

        foreach((array) $usersDB as $userDB)
        {
            foreach((array) $usersLDAP as $userLDAP)
            {
                $exists = FALSE;
                if ($userDB->getLogin() == $userLDAP->getLogin())
                {
                    $exists = TRUE;
                    break;
                }
            }
            if (!$exists)
                $usersLDAP[]=$userDB;
        }

        return $usersLDAP;

    }

    /** Projects retriever by id (relationship ProjectUser) for LDAP/PostgreSQL Hybrid.
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

    /** ProjectUser relationship entry creator by User id and Project id for LDAP/PostgreSQL Hybrid.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addProjectUser($userId, $projectId) {

        $dao = DAOFactory::getProjectUserDAO();
        return $dao->create($userId, $projectId);

    }

    /** ProjectUser relationship entry deleter by User id and Project id for LDAP/PostgreSQL Hybrid.
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

    /** Projects retriever by id (relationship Works) for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from Project table that are assigned through relationship Works to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see WorksDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getProjectsWorks($userId) {

        $dao = DAOFactory::getWorksDAO();
        return $dao->getByUserId($userId);

    }

    /** Works relationship entry creator by User id and Project id for LDAP/PostgreSQL Hybrid.
     *
     * This function creates a new entry in the table Works (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addProjectWorks($userId, $projectId) {

        $dao = DAOFactory::getWorksDAO();
        return $dao->create($userId, $projectId);

    }

    /** Works relationship entry deleter by User id and Project id for LDAP/PostgreSQL Hybrid.
     *
     * This function deletes a entry in the table Works (that represents that relationship between Users and Projects)
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see WorksDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeProjectWorks($userId, $projectId) {

        $dao = DAOFactory::getWorksDAO();
        return $dao->delete($userId, $projectId);

    }

    /** User Groups retriever by id for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from User Group table that are assigned through relationship Belongs to the User with
     * the id <var>$userId</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link LDAPInvalidOperationException}
     */
    public function getGroups($userId) {

        $dao = DAOFactory::getBelongsDAO();
        return $dao->getByUserId($userId);

    }

    /** User Groups retriever by login for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from User Group that are assigned through relationship Belongs to the User with
     * the login <var>$userLogin</var> and creates a {@link UserGroupVO} with data from each row.
     *
     * @param int $userLogin the login of the User whose User Groups we want to retrieve.
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link LDAPOperationErrorException}
     */
    public function getGroupsByLogin($userLogin) {

        $dao = DAOFactory::getBelongsDAO();
        return $dao->getByUserLogin($userLogin);

    }

    /** Belongs relationship entry creator by User id and User Group id for LDAP/PostgreSQL Hybrid.
     *
     * This function creates a new entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User we want to relate to the User Group.
     * @param int $groupId the id of the User Group we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link LDAPInvalidOperationException}
     */
    public function addGroup($userId, $groupId) {

        $dao = DAOFactory::getBelongsDAO();
        return $dao->create($userId, $groupId);

    }

    /** Belongs relationship entry deleter by User id and Project id for LDAP/PostgreSQL Hybrid.
     *
     * This function deletes a entry in the table Belongs (that represents that relationship between Users and User Groups)
     * with the User id <var>$userId</var> and the User Group id <var>$groupId</var>.
     *
     * @param int $userId the id of the User whose relation to the User Group we want to delete.
     * @param int $groupId the id of the User Group whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see BelongsDAO, UserGroupDAO
     * @throws {@link LDAPInvalidOperationException}
     */
    public function removeGroup($userId, $groupId) {

        $dao = DAOFactory::getBelongsDAO();
        return $dao->delete($userId, $groupId);

    }

    /** Tasks retriever by id for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getTasks($userId) {

        $dao = DAOFactory::getTaskDAO();
        return $dao->getByUserId($userId);

    }

    /** Extra Hours retriever by id for LDAP/PostgreSQL Hybrid.
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

    /** Custom Events retriever by id for LDAP/PostgreSQL Hybrid.
     *
     * This function retrieves the rows from Custom Event table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CustomEventVO} with data from each row.
     *
     * @param int $userId the id of the User whose Custom Events we want to retrieve.
     * @return array an array with value objects {@link CustomEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomEventDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCustomEvents($userId) {

        $dao = DAOFactory::getCustomEventDAO();
        return $dao->getByUserId($userId);

    }

    /** Area History retriever by id for LDAP/PostgreSQL Hybrid.
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

    /** Hour Cost History retriever by id for LDAP/PostgreSQL Hybrid.
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

    /** City History retriever by id for LDAP/PostgreSQL Hybrid.
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

    /** Journey History retriever by id for LDAP/PostgreSQL Hybrid.
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

    /** User updater for LDAP/PostgreSQL Hybrid.
     *
     * This function updates the data of a User by its {@link UserVO}.
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
        if(sizeof($currUserVO) > 0) {

            $sql = "UPDATE usr SET login=" . DBPostgres::checkStringNull($userVO->getLogin()) . ", password=" . DBPostgres::checkStringNull($userVO->getPassword()) . " WHERE id=" .$userVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_usr_login"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** User creator for LDAP/PostgreSQL Hybrid.
     *
     * This function creates a new row for a User by its {@link UserVO}, only if the user already exists in LDAP.
     * The internal id of <var>$userVO</var> will be set after its creation.
     *
     * @param UserVO $userVO the {@link UserVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(UserVO $userVO) {
        $affectedRows = 0;

        if (!$sr=ldap_read($this->ldapConnect,"ou=People," .
                ConfigurationParametersManager::getParameter('LDAP_BASE'),
                "(uid=" . $userVO->getLogin() . ")",
                array("uid")
            )) {
            return $affectedRows;
        }

        $sql = "INSERT INTO usr (login) VALUES(" . DBPostgres::checkStringNull($userVO->getLogin()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_usr_login"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $userVO->setID(DBPostgres::getId($this->connect, "usr_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** User deleter for LDAP/PostgreSQL Hybrid.
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
        if(sizeof($currUserVO) > 0) {
            $sql = "DELETE FROM usr WHERE id=".$userVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new HybridUserDAO();

$groups = $dao->getGroupsByLogin("jaragunde");

foreach ($groups as $group)
    print $group->getName() . "\n";

$users = $dao->getAll();

foreach ($users as $user)
{
    print $user->getLogin() . "\n";
    print $user->getId() . "\n";
}

$user = $dao->login("jlopez", "****");

print("\n" . $user->getLogin() . "\n");

*/
