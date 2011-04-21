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


/** File for PostgreSQLWorksDAO
 *
 *  This file just contains {@link PostgreSQLWorksDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/WorksDAO/WorksDAO.php');

/** DAO for relationship Works in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link WorksDAO}.
 *
 * @see WorksDAO
 */
class PostgreSQLWorksDAO extends WorksDAO {

    /** Works DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link WorksDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see WorksDAO::__construct()
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

        $userVO->setId($row[id]);
        $userVO->setLogin($row[login]);
        $userVO->setPassword($row[password]);

    return $userVO;
    }

    /** Value object constructor from edge B for PostgreSQL.
     *
     * This function creates a new {@link ProjectVO} with data retrieved from database edge B (Project).
     *
     * @param array $row an array with the values from a row.
     * @return ProjectVO a {@link ProjectVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setBValues($row)
    {

    $projectVO = new ProjectVO();

        $projectVO->setId($row[id]);
        $projectVO->setActivation($row[activation]);
    $projectVO->setInit(date_create($row[init]));
        $projectVO->setEnd(date_create($row[_end]));
        $projectVO->setInvoice($row[invoice]);
        $projectVO->setEstHours($row[est_hours]);
    $projectVO->setAreaId($row[areaid]);
        $projectVO->setType($row[type]);
        $projectVO->setDescription($row[description]);
        $projectVO->setMovedHours($row[moved_hours]);
    $projectVO->setSchedType($row[sched_type]);

    return $projectVO;
    }

    /** Works entry retriever by id's.
     *
     * This function retrieves the row from Works table with the id's <var>$userId</var> and <var>$projectId</var>.
     *
     * @param int $userId the id (that matches with a User) of the row we want to retrieve.
     * @param int $projectId the id (that matches with a Project) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link SQLQueryErrorException}
     */
    protected function getByIds($userId, $projectId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM works WHERE usrid=" . $userId . " AND projectid=" . $projectId;
    $result = $this->executeFromA($sql);
    return $result;
    }

    /** Projects retriever by User id.
     *
     * This function retrieves the rows from Project table that are assigned through relationship Works to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see UserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT project.* FROM works LEFT JOIN project ON works.projectid=project.id WHERE works.usrid=" . $userId . " ORDER BY project.id ASC";
    $result = $this->executeFromA($sql);
    return $result;
    }

    /** Users retriever by Project id.
     *
     * This function retrieves the rows from User table that are assigned through relationship Works to the Project with
     * the id <var>$projectId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByProjectId($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT usr.* FROM works LEFT JOIN usr ON works.usrid=usr.id WHERE works.projectid=" . $projectId . " ORDER BY usr.id ASC";
    $result = $this->executeFromB($sql);
    return $result;
    }

    /** Works relationship entry creator by User id and Project id.
     *
     * This function creates a new entry in the table Works
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the User.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function create($userId, $projectId) {
    $affectedRows = 0;

        // Check for a works entry ID.
        $currBelongs = $this->getByIds($userId, $projectId);

        // If it doesn't exist, then create.
        if(sizeof($currBelongs) == 0) {
        $sql = "INSERT INTO works (usrid, projectid) VALUES (" . $userId . ", " . $projectId . ")";

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $affectedRows = pg_affected_rows($res);
    }

    return $affectedRows;

    }

    /** Works relationship entry deleter by User id and Project id.
     *
     * This function deletes a entry in the table Works
     * with the User id <var>$userId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see UserDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function delete($userId, $projectId) {
        $affectedRows = 0;

        // Check for a works entry ID.
        $currBelongs = $this->getByIds($userId, $projectId);

        // If it exists, then delete.
        if(sizeof($currBelongs) > 0) {
            $sql = "DELETE FROM works WHERE usrid=" . $userId . " AND projectid=" . $projectId;

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLWorksDAO();

// We create a new entry

$userId = 1;

$projectId = 1;

$dao->create($userId, $projectId);

// We search for the new entry from side A

$projects = $dao->getByUserId($userId);

foreach ($projects as $project)
    print ("Project for user ". $userId ." : " . $project->getDescription() . "\n");

// We search for the new entry from side B

$users = $dao->getByProjectId($projectId);

foreach ($users as $user)
    print ("User for project ". $projectId ." : " . $user->getLogin() . "\n");

// We delete the new entry

$dao->delete($userId, $projectId);*/
