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


/** File for PostgreSQLTaskSectionDAO
 *
 *  This file just contains {@link PostgreSQLTaskSectionDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskSectionDAO/TaskSectionDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Task Stories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskSectionDAO}.
 *
 * @see TaskSectionDAO, TaskSectionVO
 */
class PostgreSQLTaskSectionDAO extends TaskSectionDAO{

    /** TaskSection DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link TaskSectionDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see TaskSectionDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** TaskSection value object constructor for PostgreSQL.
     *
     * This function creates a new {@link TaskSectionVO} with data retrieved from database.
     *
     * @param array $row an array with the TaskSection values from a row.
     * @return TaskSectionVO a {@link TaskSectionVO} with its properties set to the values from <var>$row</var>.
     * @see TaskSectionVO
     */
    protected function setValues($row)
    {

    $taskSectionVO = new TaskSectionVO();

    $taskSectionVO->setId($row[id]);
    $taskSectionVO->setName($row[name]);
    $taskSectionVO->setRisk($row[risk]);
    $taskSectionVO->setEstHours($row[est_hours]);
    $taskSectionVO->setSectionId($row[sectionid]);
    $taskSectionVO->setUserId($row[usrid]);

    return $taskSectionVO;
    }

    /** TaskSection retriever by id for PostgreSQL.
     *
     * This function retrieves the row from TaskSection table with the id <var>$taskSectionId</var> and creates a {@link TaskSectionVO} with its data.
     *
     * @param int $taskSectionId the id of the row we want to retrieve.
     * @return TaskSectionVO a value object {@link TaskSectionVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($taskSectionId) {
        if (!is_numeric($taskSectionId))
        throw new SQLIncorrectTypeException($taskSectionId);
        $sql = "SELECT * FROM task_section WHERE id=".$taskSectionId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** TaskStories retriever by Section id.
     *
     * This function retrieves the rows from TaskSection table that are associated with the Section with
     * the id <var>$sectionId</var> and creates an {@link TaskSectionVO} with data from each row.
     *
     * @param int $sectionId the id of the Section whose TaskStories we want to retrieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getBySectionId($sectionId) {
    if (!is_numeric($sectionId))
        throw new SQLIncorrectTypeException($sectionId);
        $sql = "SELECT * FROM task_section WHERE sectionid=".$sectionId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** TaskStories retriever for PostgreSQL.
     *
     * This function retrieves all rows from TaskSection table and creates a {@link TaskSectionVO} with data from each row.
     *
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM task_section ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** TaskSections retriever by Story id.
     *
     * This function retrieves the rows from TaskSection table that are associated with the Story with
     * the id <var>$storyId</var> (through their Project) and creates a {@link TaskSectionVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose Task Sections we want to retrieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByStoryId($storyId) {
        if (!is_numeric($storyId))
            throw new SQLIncorrectTypeException($storyId);
        $sql = "SELECT task_section.* FROM task_section JOIN section ON sectionid=section.id JOIN module ON moduleid=module.id JOIN project ON module.projectid=project.id JOIN iteration ON iteration.projectid=project.id JOIN story ON iterationid=iteration.id WHERE story.id=" . $storyId . " ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** TaskSection updater for PostgreSQL.
     *
     * This function updates the data of a TaskSection by its {@link TaskSectionVO}.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(TaskSectionVO $taskSectionVO) {
        $affectedRows = 0;

        if($taskSectionVO->getId() != "") {
            $currTaskSectionVO = $this->getById($taskSectionVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currTaskSectionVO) > 0) {

        $sql = "UPDATE task_section SET name=" . DBPostgres::checkStringNull($taskSectionVO->getName()) . ", risk=" . DBPostgres::checkNull($taskSectionVO->getRisk()) . ", est_hours=" . DBPostgres::checkNull($taskSectionVO->getEstHours()) . ", sectionid=" . DBPostgres::checkNull($taskSectionVO->getSectionId()) . ", usrid=" . DBPostgres::checkNull($taskSectionVO->getUserId()) . " WHERE id=".$taskSectionVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_task_section_section_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** TaskSection creator for PostgreSQL.
     *
     * This function creates a new row for a TaskSection by its {@link TaskSectionVO}.
     * The internal id of <var>$taskSectionVO</var> will be set after its creation.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(TaskSectionVO $taskSectionVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO task_section (name, risk, est_hours, sectionid, usrid) VALUES(" . DBPostgres::checkStringNull($taskSectionVO->getName()) . ", " . DBPostgres::checkNull($taskSectionVO->getRisk()) . ", " . DBPostgres::checkNull($taskSectionVO->getEstHours()) . ", ". DBPostgres::checkNull($taskSectionVO->getSectionId()) . "," . DBPostgres::checkNull($taskSectionVO->getUserId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_task_section_section_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $taskSectionVO->setId(DBPostgres::getId($this->connect, "task_section_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** TaskSection deleter for PostgreSQL.
     *
     * This function deletes the data of a TaskSection by its {@link TaskSectionVO}.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(TaskSectionVO $taskSectionVO) {
        $affectedRows = 0;

        // Check for a taskSection ID.
        if($taskSectionVO->getId() >= 0) {
            $currTaskSectionVO = $this->getById($taskSectionVO->getId());
        }

        // Otherwise delete a taskSection.
        if(sizeof($currTaskSectionVO) > 0) {
            $sql = "DELETE FROM task_section WHERE id=".$taskSectionVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLTaskSectionDAO();

var_dump($dao->getByStoryId(3));

// We create a new task section

$tasksection = new TaskSectionVO();

$tasksection->setName("Very well");
$tasksection->setInit(date_create("2009-06-01"));
$tasksection->setSectionId(2);
$tasksection->setUserId(2);

$dao->create($tasksection);

print ("New tasksection Id is ". $tasksection->getId() ."\n");

// We search for the old text

$tasksection = $dao->getById($tasksection->getId());

print ("Old text found is ". $tasksection->getName() ."\n");

// We update the module with a different text

$tasksection->setName("New text");

$dao->update($tasksection);

// We search for the new text

$tasksection = $dao->getById($tasksection->getId());

print ("New text found is ". $tasksection->getName() ."\n");

// We delete the new section

//$dao->delete($tasksection);*/
