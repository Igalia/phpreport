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


/** File for PostgreSQLIterationDAO
 *
 *  This file just contains {@link PostgreSQLIterationDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/IterationDAO/IterationDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Iterations in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link IterationDAO}.
 *
 * @see IterationDAO, IterationVO
 */
class PostgreSQLIterationDAO extends IterationDAO{

    /** Iteration DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link IterationDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see IterationDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Iteration value object constructor for PostgreSQL.
     *
     * This function creates a new {@link IterationVO} with data retrieved from database.
     *
     * @param array $row an array with the Iteration values from a row.
     * @return IterationVO a {@link IterationVO} with its properties set to the values from <var>$row</var>.
     * @see IterationVO
     */
    protected function setValues($row)
    {

    $iterationVO = new IterationVO();

    $iterationVO->setId($row[id]);
    $iterationVO->setName($row[name]);
    $iterationVO->setInit(date_create($row[init]));
    $iterationVO->setEnd(date_create($row[_end]));
    $iterationVO->setSummary($row[summary]);
    $iterationVO->setProjectId($row[projectid]);

    return $iterationVO;
    }

    /** Iteration retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Iteration table with the id <var>$iterationId</var> and creates a {@link IterationVO} with its data.
     *
     * @param int $iterationId the id of the row we want to retrieve.
     * @return IterationVO a value object {@link IterationVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($iterationId) {
        if (!is_numeric($iterationId))
        throw new SQLIncorrectTypeException($iterationId);
        $sql = "SELECT * FROM iteration WHERE id=".$iterationId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Iterations retriever by Project id.
     *
     * This function retrieves the rows from Iteration table that are associated with the Project with
     * the id <var>$projectId</var> and creates an {@link IterationVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Iterations we want to retrieve.
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByProjectId($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM iteration WHERE projectid=".$projectId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Iterations retriever for PostgreSQL.
     *
     * This function retrieves all rows from Iteration table and creates a {@link IterationVO} with data from each row.
     *
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM iteration ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Stories retriever by Iteration id for PostgreSQL.
     *
     * This function retrieves the rows from Story table that are assigned through relationship Contains to the Iteration with
     * the id <var>$iterationId</var> and creates a {@link StoryVO} with data from each row.
     *
     * @param int $iterationId the id of the Iteration whose Stories we want to retrieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see StoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getStories($iterationId) {

        $dao = DAOFactory::getStoryDAO();
        return $dao->getByIterationId($iterationId);

    }

    /** Iteration updater for PostgreSQL.
     *
     * This function updates the data of a Iteration by its {@link IterationVO}.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(IterationVO $iterationVO) {
        $affectedRows = 0;

        if($iterationVO->getId() != "") {
            $currIterationVO = $this->getById($iterationVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currIterationVO) > 0) {

            $sql = "UPDATE iteration SET name=" . DBPostgres::checkStringNull($iterationVO->getName()) . ", init=" . DBPostgres::formatDate($iterationVO->getInit()) . ", _end=" . DBPostgres::formatDate($iterationVO->getEnd()) . ", summary=" . DBPostgres::checkStringNull($iterationVO->getSummary()) . ", projectid=" . DBPostgres::checkNull($iterationVO->getProjectId()) . " WHERE id=".$iterationVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_iteration_project_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Iteration creator for PostgreSQL.
     *
     * This function creates a new row for a Iteration by its {@link IterationVO}.
     * The internal id of <var>$iterationVO</var> will be set after its creation.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(IterationVO $iterationVO) {

    $affectedRows = 0;

    $sql = "INSERT INTO iteration (name, init, _end, summary, projectid) VALUES(" . DBPostgres::checkStringNull($iterationVO->getName()) . ", " . DBPostgres::formatDate($iterationVO->getInit()) . ", " . DBPostgres::formatDate($iterationVO->getEnd()) . ", " . DBPostgres::checkStringNull($iterationVO->getSummary()) . ", " . DBPostgres::checkNull($iterationVO->getProjectId()) .")";

    $res = pg_query($this->connect, $sql);

    if ($res == NULL)
        if (strpos(pg_last_error(), "unique_iteration_project_name"))
            throw new SQLUniqueViolationException(pg_last_error());
        else throw new SQLQueryErrorException(pg_last_error());

    $iterationVO->setId(DBPostgres::getId($this->connect, "iteration_id_seq"));

    $affectedRows = pg_affected_rows($res);

    return $affectedRows;

    }

    /** Iteration deleter for PostgreSQL.
     *
     * This function deletes the data of a Iteration by its {@link IterationVO}.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(IterationVO $iterationVO) {
        $affectedRows = 0;

        // Check for a iteration ID.
        if($iterationVO->getId() >= 0) {
            $currIterationVO = $this->getById($iterationVO->getId());
        }

        // Otherwise delete a iteration.
        if(sizeof($currIterationVO) > 0) {
            $sql = "DELETE FROM iteration WHERE id=".$iterationVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




//Uncomment these lines in order to do a simple test of the Dao


/*$dao = new PostgreSQLIterationDAO();

// We create a new iteration

$iteration = new IterationVO();

$iteration->setInit(date_create('2009-01-05'));
$iteration->setEnd(date_create('2009-01-15'));
$iteration->setName("Very well");
$iteration->setSummary("Old text");
$iteration->setProjectId(1);

$dao->create($iteration);*/

/*print ("New iteration Id is ". $iteration->getId() ."\n");

// We search for the old text

$iteration = $dao->getById($iteration->getId());

print ("Old text found is ". $iteration->getName() ."\n");

// We update the iteration with a different text

$iteration->setName("New text");

$dao->update($iteration);

// We search for the new text

$iteration = $dao->getById($iteration->getId());

print ("New text found is ". $iteration->getName() ."\n");

// We delete the new iteration

//$dao->delete($iteration);*/
