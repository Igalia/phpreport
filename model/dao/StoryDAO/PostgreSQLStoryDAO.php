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


/** File for PostgreSQLStoryDAO
 *
 *  This file just contains {@link PostgreSQLStoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/StoryDAO/StoryDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Stories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link StoryDAO}.
 *
 * @see StoryDAO, StoryVO
 */
class PostgreSQLStoryDAO extends StoryDAO{

    /** Story DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link StoryDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see StoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Story value object constructor for PostgreSQL.
     *
     * This function creates a new {@link StoryVO} with data retrieved from database.
     *
     * @param array $row an array with the Story values from a row.
     * @return StoryVO a {@link StoryVO} with its properties set to the values from <var>$row</var>.
     * @see StoryVO
     */
    protected function setValues($row)
    {

        $storyVO = new StoryVO();

        $storyVO->setId($row[id]);

        if (strtolower($row[accepted]) == "t")
            $storyVO->setAccepted(True);
        elseif (strtolower($row[accepted]) == "f")
            $storyVO->setAccepted(False);

        $storyVO->setName($row[name]);
        $storyVO->setUserId($row[usrid]);
        $storyVO->setStoryId($row[storyid]);
        $storyVO->setIterationId($row[iterationid]);

        return $storyVO;
    }

    /** Story retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Story table with the id <var>$storyId</var> and creates a {@link StoryVO} with its data.
     *
     * @param int $storyId the id of the row we want to retrieve.
     * @return StoryVO a value object {@link StoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($storyId) {
        if (!is_numeric($storyId))
        throw new SQLIncorrectTypeException($storyId);
        $sql = "SELECT * FROM story WHERE id=".$storyId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Stories retriever by Iteration id.
     *
     * This function retrieves the rows from Story table that are associated with the Iteration with
     * the id <var>$iterationId</var> and creates an {@link StoryVO} with data from each row.
     *
     * @param int $iterationId the id of the Iteration whose Stories we want to retrieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByIterationId($iterationId) {
    if (!is_numeric($iterationId))
        throw new SQLIncorrectTypeException($iterationId);
        $sql = "SELECT * FROM story WHERE iterationid=".$iterationId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Storys retriever for PostgreSQL.
     *
     * This function retrieves all rows from Story table and creates a {@link StoryVO} with data from each row.
     *
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM story ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** TaskStories retriever by Story id for PostgreSQL.
     *
     * This function retrieves the rows from TaskStory table that are assigned through relationship Contains to the Story with
     * the id <var>$storyId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose TaskStories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskStoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getTaskStories($storyId) {

        $dao = DAOFactory::getTaskStoryDAO();
        return $dao->getByStoryId($storyId);

    }

    /** Story updater for PostgreSQL.
     *
     * This function updates the data of a Story by its {@link StoryVO}.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(StoryVO $storyVO) {
        $affectedRows = 0;

        if($storyVO->getId() != "") {
            $currStoryVO = $this->getById($storyVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currStoryVO) > 0) {

            $sql = "UPDATE story SET name=" . DBPostgres::checkStringNull($storyVO->getName()) . ", accepted=" . DBPostgres::boolToString($storyVO->getAccepted()) . ", usrid=" . DBPostgres::checkNull($storyVO->getUserId()) . ", iterationid=" . DBPostgres::checkNull($storyVO->getiterationId()) . ", storyid=" . DBPostgres::checkNull($storyVO->getStoryId()) . " WHERE id=".$storyVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_story_iteration_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Story creator for PostgreSQL.
     *
     * This function creates a new row for a Story by its {@link StoryVO}.
     * The internal id of <var>$storyVO</var> will be set after its creation.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(StoryVO $storyVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO story (name, accepted, usrid, iterationid, storyid) VALUES(" . DBPostgres::checkStringNull($storyVO->getName()) . ", " . DBPostgres::boolToString($storyVO->getAccepted()) . ", " . DBPostgres::checkNull($storyVO->getUserId()) . ", " . DBPostgres::checkNull($storyVO->getIterationId()) . ", " . DBPostgres::checkNull($storyVO->getStoryId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_story_iteration_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $storyVO->setId(DBPostgres::getId($this->connect, "story_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Story deleter for PostgreSQL.
     *
     * This function deletes the data of a Story by its {@link StoryVO}.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(StoryVO $storyVO) {
        $affectedRows = 0;

        // Check for a story ID.
        if($storyVO->getId() >= 0) {
            $currStoryVO = $this->getById($storyVO->getId());
        }

        // Otherwise delete a story.
        if(sizeof($currStoryVO) > 0) {
            $sql = "DELETE FROM story WHERE id=".$storyVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLStoryDAO();

// We create a new story

$story = new StoryVO();

$story->setName("Very well");
$story->setIterationId(1);

$dao->create($story);

print ("New story Id is ". $story->getId() ."\n");

// We search for the old text

$story = $dao->getById($story->getId());

print ("Old text found is ". $story->getName() ."\n");

// We update the iteration with a different text

$story->setName("New text");

$dao->update($story);

// We search for the new text

$story = $dao->getById($story->getId());

print ("New text found is ". $story->getName() ."\n");

// We delete the new story

//$dao->delete($story);*/
