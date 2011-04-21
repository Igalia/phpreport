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


/** File for PostgreSQLTaskStoryDAO
 *
 *  This file just contains {@link PostgreSQLTaskStoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskStoryDAO/TaskStoryDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Task Stories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskStoryDAO}.
 *
 * @see TaskStoryDAO, TaskStoryVO
 */
class PostgreSQLTaskStoryDAO extends TaskStoryDAO{

    /** TaskStory DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link TaskStoryDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see TaskStoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** TaskStory value object constructor for PostgreSQL.
     *
     * This function creates a new {@link TaskStoryVO} with data retrieved from database.
     *
     * @param array $row an array with the TaskStory values from a row.
     * @return TaskStoryVO a {@link TaskStoryVO} with its properties set to the values from <var>$row</var>.
     * @see TaskStoryVO
     */
    protected function setValues($row)
    {

    $taskStoryVO = new TaskStoryVO();

    $taskStoryVO->setId($row[id]);
    $taskStoryVO->setRisk($row[risk]);
    $taskStoryVO->setName($row[name]);
    $taskStoryVO->setEstHours($row[est_hours]);
    $taskStoryVO->setToDo($row[to_do]);
    if ($row[est_end])
        $taskStoryVO->setEstEnd(date_create($row[est_end]));
    if ($row[_end])
        $taskStoryVO->setEnd(date_create($row[_end]));
    if ($row[init])
        $taskStoryVO->setInit(date_create($row[init]));
    $taskStoryVO->setStoryId($row[storyid]);
    $taskStoryVO->setUserId($row[usrid]);
    $taskStoryVO->setTaskSectionId($row[task_sectionid]);

    return $taskStoryVO;
    }

    /** TaskStory retriever by id for PostgreSQL.
     *
     * This function retrieves the row from TaskStory table with the id <var>$taskStoryId</var> and creates a {@link TaskStoryVO} with its data.
     *
     * @param int $taskStoryId the id of the row we want to retrieve.
     * @return TaskStoryVO a value object {@link TaskStoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($taskStoryId) {
        if (!is_numeric($taskStoryId))
        throw new SQLIncorrectTypeException($taskStoryId);
        $sql = "SELECT * FROM task_story WHERE id=".$taskStoryId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** TaskStories retriever by Task Section id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Task Section with
     * the id <var>$taskSectionId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $taskSectionId the id of the Task Section whose Task Stories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByTaskSectionId($taskSectionId) {
        if (!is_numeric($taskSectionId))
            throw new SQLIncorrectTypeException($taskSectionId);
            $sql = "SELECT * FROM task_story WHERE task_sectionid=" . $taskSectionId . " ORDER BY id ASC";
        $result = $this->execute($sql);
        return $result;

    }

    /** TaskStories retriever by Section id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Section with
     * the id <var>$sectionId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $sectionId the id of the Section whose Task Stories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getBySectionId($sectionId) {

        if (!is_numeric($sectionId))
            throw new SQLIncorrectTypeException($sectionId);
            $sql = "SELECT * FROM task_story WHERE task_sectionid IN (SELECT id FROM task_section WHERE sectionid = " . $sectionId . ") ORDER BY id ASC";
        $result = $this->execute($sql);
        return $result;

    }

    /** TaskStories retriever by Story id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Story with
     * the id <var>$storyId</var> and creates an {@link TaskStoryVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose TaskStories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByStoryId($storyId) {
    if (!is_numeric($storyId))
        throw new SQLIncorrectTypeException($storyId);
        $sql = "SELECT * FROM task_story WHERE storyid=".$storyId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** TaskStories retriever for PostgreSQL.
     *
     * This function retrieves all rows from TaskStory table and creates a {@link TaskStoryVO} with data from each row.
     *
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM task_story ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Open TaskStories retriever for PostgreSQL.
     *
     * This function retrieves all rows from TaskStory table that don't have an ending date assigned and creates
     * a {@link TaskStoryVO} with data from each row. We can pass optional parameters for filtering by User, <var>$userId</var>,
     * and by Project, <var>$projectId</var>.
     *
     * @param int $userId optional parameter for filtering by User.
     * @param int $projectId optional parameter for filtering by Project.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getOpen($userId = NULL, $projectId = NULL) {
        $sql = "SELECT * FROM task_story WHERE _end IS NULL ";

        if ($userId != NULL)
            if (!is_numeric($userId))
                throw new SQLIncorrectTypeException($userId);
            else
                $sql = $sql . "AND usrid = " . $userId . " ";

        if ($projectId != NULL)
            if (!is_numeric($projectId))
                throw new SQLIncorrectTypeException($projectId);
            else
                $sql = $sql . "AND storyid IN (SELECT story.id FROM story JOIN iteration ON iteration.id = story.iterationid WHERE iteration.projectid = " . $projectId . ") ";

        $sql = $sql . "ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** TaskStory updater for PostgreSQL.
     *
     * This function updates the data of a TaskStory by its {@link TaskStoryVO}.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(TaskStoryVO $taskStoryVO) {
        $affectedRows = 0;

        if($taskStoryVO->getId() != "") {
            $currTaskStoryVO = $this->getById($taskStoryVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currTaskStoryVO) > 0) {

        $sql = "UPDATE task_story SET name=" . DBPostgres::checkStringNull($taskStoryVO->getName()) . ", risk=" . DBPostgres::checkNull($taskStoryVO->getRisk()) . ", est_hours=" . DBPostgres::checkNull($taskStoryVO->getEstHours()) . ", to_do=" . DBPostgres::checkNull($taskStoryVO->getToDo()) . ", est_end=" . DBPostgres::formatDate($taskStoryVO->getEstEnd()) . ", storyid=" . DBPostgres::checkNull($taskStoryVO->getStoryId()) . ", usrid=" . DBPostgres::checkNull($taskStoryVO->getUserId()) . ", task_sectionid=" . DBPostgres::checkNull($taskStoryVO->getTaskSectionId()) . ", _end=" . DBPostgres::formatDate($taskStoryVO->getEnd()) . ", init=" . DBPostgres::formatDate($taskStoryVO->getInit()) . " WHERE id=".$taskStoryVO->getId();

            $res = pg_query($this->connect, $sql);
            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_task_story_story_name"))
                        throw new SQLUniqueViolationException(pg_last_error());
                    else throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** TaskStory creator for PostgreSQL.
     *
     * This function creates a new row for a TaskStory by its {@link TaskStoryVO}.
     * The internal id of <var>$taskStoryVO</var> will be set after its creation.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(TaskStoryVO $taskStoryVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO task_story (name, risk, est_hours, to_do, est_end, storyid, _end, init, usrid, task_sectionid) VALUES(" . DBPostgres::checkStringNull($taskStoryVO->getName()) . ", " . DBPostgres::checkNull($taskStoryVO->getRisk()) . ", " . DBPostgres::checkNull($taskStoryVO->getEstHours()) . ", " . DBPostgres::checkNull($taskStoryVO->getToDo()) . ", " . DBPostgres::formatDate($taskStoryVO->getEstEnd()) . ", " . DBPostgres::checkNull($taskStoryVO->getStoryId()) . ", " . DBPostgres::formatDate($taskStoryVO->getEnd()) . ", " . DBPostgres::formatDate($taskStoryVO->getInit()) . "," . DBPostgres::checkNull($taskStoryVO->getUserId()) . ", " . DBPostgres::checkNull($taskStoryVO->getTaskSectionId()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_task_story_story_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $taskStoryVO->setId(DBPostgres::getId($this->connect, "task_story_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** TaskStory deleter for PostgreSQL.
     *
     * This function deletes the data of a TaskStory by its {@link TaskStoryVO}.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(TaskStoryVO $taskStoryVO) {
        $affectedRows = 0;

        // Check for a taskStory ID.
        if($taskStoryVO->getId() >= 0) {
            $currTaskStoryVO = $this->getById($taskStoryVO->getId());
        }

        // Otherwise delete a taskStory.
        if(sizeof($currTaskStoryVO) > 0) {
            $sql = "DELETE FROM task_story WHERE id=".$taskStoryVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLTaskStoryDAO();

// We create a new task story

$taskstory = new TaskStoryVO();

$taskstory->setName("Very well");
$taskstory->setInit(date_create("2009-06-01"));
$taskstory->setStoryId(2);
$taskstory->setUserId(2);

$dao->create($taskstory);

print ("New taskstory Id is ". $taskstory->getId() ."\n");

// We search for the old text

$taskstory = $dao->getById($taskstory->getId());

print ("Old text found is ". $taskstory->getName() ."\n");

// We update the iteration with a different text

$taskstory->setName("New text");

$dao->update($taskstory);

// We search for the new text

$taskstory = $dao->getById($taskstory->getId());

print ("New text found is ". $taskstory->getName() ."\n");

// We delete the new story

//$dao->delete($taskstory);*/
