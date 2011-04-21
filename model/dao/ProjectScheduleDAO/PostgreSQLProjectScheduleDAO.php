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


/** File for PostgreSQLProjectScheduleDAO
 *
 *  This file just contains {@link PostgreSQLProjectScheduleDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectScheduleVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectScheduleDAO/ProjectScheduleDAO.php');

/** DAO for Project Schedules in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link ProjectScheduleDAO}.
 *
 * @see ProjectScheduleDAO, ProjectScheduleVO
 */
class PostgreSQLProjectScheduleDAO extends ProjectScheduleDAO{

    /** Project Schedule DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link ProjectScheduleDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see ProjectScheduleDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Project Schedule value object constructor for PostgreSQL.
     *
     * This function creates a new {@link ProjectScheduleVO} with data retrieved from database.
     *
     * @param array $row an array with the Project Schedule values from a row.
     * @return ProjectScheduleVO a {@link ProjectScheduleVO} with its properties set to the values from <var>$row</var>.
     * @see ProjectScheduleVO
     */
    protected function setValues($row)
    {

    $projectScheduleVO = new ProjectScheduleVO();

        $projectScheduleVO->setId($row[id]);
        $projectScheduleVO->setWeeklyLoad($row[weekly_load]);
        $projectScheduleVO->setInitWeek($row[init_week]);
    $projectScheduleVO->setInitYear($row[init_year]);
        $projectScheduleVO->setEndWeek($row[end_week]);
        $projectScheduleVO->setEndYear($row[end_year]);
        $projectScheduleVO->setUserId($row[usrid]);
        $projectScheduleVO->setProjectId($row[projectid]);

    return $projectScheduleVO;
    }

    /** Project Schedule retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Project Schedule table with the id <var>$projectScheduleId</var> and
     * creates a {@link ProjectScheduleVO} with its data.
     *
     * @param int $projectScheduleId the id of the row we want to retrieve.
     * @return ProjectScheduleVO a value object {@link ProjectScheduleVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($projectScheduleId) {
    if (!is_numeric($projectScheduleId))
        throw new SQLIncorrectTypeException($areaId);
        $sql = "SELECT * FROM project_schedule WHERE id=".$projectScheduleId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Project Schedules retriever by User id and Project id for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are associated with the User with
     * the id <var>$userId</var> and the Project with <var>$projectId</var> and creates a {@link ProjectScheduleVO} with data from each row.
     *
     * @param int $userId the id of the User whose Project Schedules we want to retrieve.
     * @param int $projectId the id of the Project whose Project Schedules we want to retrieve.
     * @return array an array with value objects {@link ProjectScheduleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserProjectIds($userId, $projectId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM project_schedule WHERE projectid=".$projectId . " AND usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Project Schedules retriever by User id, Project id, init week and init year for PostgreSQL.
     *
     * This function retrieves the row from Project table that is associated with the User with
     * the id <var>$userId</var> and the Project with <var>$projectId</var>, and that is scheduled to start on week <var>$initWeek</var>
     * of year <var>$initYear</var>, and creates a {@link ProjectScheduleVO} with data from the row.
     *
     * @param int $userId the id of the User whose Project Schedules we want to retrieve.
     * @param int $projectId the id of the Project whose Project Schedules we want to retrieve.
     * @param int $initWeek the init week whose Project Schedules we want to retrieve.
     * @param int $initYear the init year whose Project Schedules we want to retrieve.
     * @return ProjectScheduleVO a value object {@link ProjectScheduleVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserProjectIdsDate($userId, $projectId, $initWeek, $initYear) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM project_schedule WHERE projectid=".$projectId . " AND usrid=" . $userId . " AND init_week=" . $initWeek . " AND init_year=" . $initYear;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Project Schedules retriever for PostgreSQL.
     *
     * This function retrieves all rows from Project Schedule table and creates a {@link ProjectScheduleVO} with data from each row.
     *
     * @return array an array with value objects {@link ProjectScheduleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
     public function getAll() {
        $sql = "SELECT * FROM project_schedule ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Project Schedule updater for PostgreSQL.
     *
     * This function updates the data of a Project Schedule by its {@link ProjectScheduleVO}.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(ProjectScheduleVO $projectScheduleVO) {
        $affectedRows = 0;

        if($projectScheduleVO->getId() >= 0) {
            $currProjectScheduleVO = $this->getById($projectScheduleVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currProjectScheduleVO) > 0) {

            $sql = "UPDATE project_schedule SET weekly_load=" . DBPostgres::checkNull($projectScheduleVO->getWeeklyLoad()) . ", init_week=" . DBPostgres::checkNull($projectScheduleVO->getInitWeek()) . ", init_year=" . DBPostgres::checkNull($projectScheduleVO->getInitYear()) . ", end_week=" . DBPostgres::checkNull($projectScheduleVO->getEndWeek()) . ", end_year=" . DBPostgres::checkNull($projectScheduleVO->getEndYear()) . ", usrid=" . DBPostgres::checkNull($projectScheduleVO->getUserId()) . ", projectid=" . DBPostgres::checkNull($projectScheduleVO->getProjectId()) . " WHERE id=".$projectScheduleVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_project_schedule_user_project_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Project Schedule creator for PostgreSQL.
     *
     * This function creates a new row for a Project Schedule by its {@link ProjectScheduleVO}.
     * The internal id of <var>$projectScheduleVO</var> will be set after its creation.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(ProjectScheduleVO $projectScheduleVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO project_schedule (weekly_load, init_week, init_year, end_week, end_year, usrid, projectid) VALUES("  . DBPostgres::checkNull($projectScheduleVO->getWeeklyLoad()) . ", " . DBPostgres::checkNull($projectScheduleVO->getInitWeek()) . ", " . DBPostgres::checkNull($projectScheduleVO->getInitYear()) . ", " . DBPostgres::checkNull($projectScheduleVO->getEndWeek()) . ", " . DBPostgres::checkNull($projectScheduleVO->getEndYear()) . ", " . DBPostgres::checkNull($projectScheduleVO->getUserId()) . ", " . DBPostgres::checkNull($projectScheduleVO->getProjectId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_project_schedule_user_project_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $projectScheduleVO->setId(DBPostgres::getId($this->connect, "project_schedule_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Project Schedule deleter for PostgreSQL.
     *
     * This function deletes the data of a Project Schedule by its {@link ProjectScheduleVO}.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(ProjectScheduleVO $projectScheduleVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($projectScheduleVO->getId() >= 0) {
            $currProjectScheduleVO = $this->getById($projectScheduleVO->getId());
        }

        // Otherwise delete a user.
        if(sizeof($currProjectScheduleVO) > 0) {
            $sql = "DELETE FROM project_schedule WHERE id=".$projectScheduleVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLProjectScheduleDAO();

// We create a new projectSchedule

$projectSchedule = new ProjectScheduleVO();

$projectSchedule->setWeeklyLoad(25.5);
$projectSchedule->setInitWeek(12);
$projectSchedule->setInitYear(2005);
$projectSchedule->setEndWeek(9);
$projectSchedule->setEndYear(2006);
$projectSchedule->setUserId(1);
$projectSchedule->setProjectId(1);


$dao->create($projectSchedule);

print ("New project schedule Id is ". $projectSchedule->getId() ."\n");

// We search for the new Id

$projectSchedule = $dao->getById($projectSchedule->getId());

print ("New project schedule Id found is ". $projectSchedule->getId() ."\n");

// We update the projectSchedule with a differente description

$projectSchedule->setInitWeek(15);

$dao->update($projectSchedule);

// We search for the new init week

$projectSchedule = $dao->getById($projectSchedule->getId());

print ("New project schedule init week found is " . $projectSchedule->getInitWeek() . "\n");

// We delete the new project schedule

$dao->delete($projectSchedule);*/
