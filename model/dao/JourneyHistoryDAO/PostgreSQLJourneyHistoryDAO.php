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


/** File for PostgreSQLJourneyHistoryDAO
 *
 *  This file just contains {@link PostgreSQLJourneyHistoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/JourneyHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/JourneyHistoryDAO/JourneyHistoryDAO.php');

/** DAO for Journey Histories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link JourneyHistoryDAO}.
 *
 * @see JourneyHistoryDAO, JourneyHistoryVO
 */
class PostgreSQLJourneyHistoryDAO extends JourneyHistoryDAO{

    /** Journey History DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link JourneyHistoryDAO}, ad it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see JourneyHistoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Journey History value object constructor for PostgreSQL.
     *
     * This function creates a new {@link JourneyHistoryVO} with data retrieved from database.
     *
     * @param array $row an array with the Journey History values from a row.
     * @return JourneyHistoryVO a {@link JourneyHistoryVO} with its properties set to the values from <var>$row</var>.
     * @see JourneyHistoryVO
     */
    protected function setValues($row)
    {

    $journeyHistoryVO = new JourneyHistoryVO();

        $journeyHistoryVO->setId($row['id']);
    $journeyHistoryVO->setInitDate(date_create($row['init_date']));
    $journeyHistoryVO->setUserId($row['usrid']);
    if (is_null($row['end_date']))
            $journeyHistoryVO->setEndDate(NULL);
    else
        $journeyHistoryVO->setEndDate(date_create($row['end_date']));
    $journeyHistoryVO->setJourney($row['journey']);

    return $journeyHistoryVO;
    }

    /** Journey History retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Journey History table with the id <var>$journeyHistoryId</var> ad creates a {@link JourneyHistoryVO} with its data.
     *
     * @param int $journeyHistoryId the id of the row we wat to retrieve.
     * @return JourneyHistoryVO a value object {@link JourneyHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($journeyHistoryId) {
    if (!is_numeric($journeyHistoryId))
        throw new SQLIncorrectTypeException($journeyHistoryId);
        $sql = "SELECT * FROM journey_history WHERE id=" . $journeyHistoryId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Journey History retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Journey History table that are associated with the User with
     * the id <var>$userId</var> ad creates a {@link JourneyHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Journey History we wat to retrieve.
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM journey_history WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Journey History current entry retriever by User id for PostgreSQL.
     *
     * This function retrieves the current row from Journey History table that is associated with the User with
     * the id <var>$userId</var> ad creates a {@link JourneyHistoryVO} with data from that row.
     *
     * @param int $userId the id of the User whose Journey History current entry we wat to retrieve.
     * @return JourneyHistoryVO a value object {@link JourneyHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getCurrentByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM journey_history WHERE usrid=" . $userId . " AND end_date IS NULL";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Journey History retriever by User id ad date interval for PostgreSQL.
     *
     * This function retrieves the rows from Journey History table that are associated with the User with
     * the id <var>$userId</var> ad that full lay inside the interval defined by <var>$init</var> ad <var>$end</var>
     * ad creates a {@link JourneyHistoryVO} with data from each row.
     * If we don't indicate a User id, then entries for all users are returned.
     *
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval (included).
     * @param int $userId the id of the User whose Journey History we wat to retrieve. It's optional.
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by the User database internal identifier and the beginning date.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByIntervals(DateTime $init, DateTime $end, $userId = NULL) {
    if (is_null($userId))
        $sql = "SELECT * FROM journey_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL))
        ORDER BY usrid ASC, init_date ASC";
    elseif (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        else
        $sql = "SELECT * FROM journey_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL) AND (usrId = $userId))
        ORDER BY usrid ASC, init_date ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Journey Histories retriever for PostgreSQL.
     *
     * This function retrieves all rows from Journey History table ad creates a {@link JourneyHistoryVO} with data from each row.
     *
     * @return array an array with value objects {@link JourneyHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM journey_history ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Journey History updater for PostgreSQL.
     *
     * This function updates the data of a Journey History by its {@link JourneyHistoryVO}.
     *
     * @param JourneyHistoryVO $journeyHistoryVO the {@link JourneyHistoryVO} with the data we wat to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(JourneyHistoryVO $journeyHistoryVO) {
        $affectedRows = 0;

        if($journeyHistoryVO->getId() >= 0) {
            $currJourneyHistoryVO = $this->getById($journeyHistoryVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currJourneyHistoryVO) > 0) {

            $sql = "UPDATE journey_history SET init_date=" . DBPostgres::formatDate($journeyHistoryVO->getInitDate()) . ", usrid=" . DBPostgres::checkNull($journeyHistoryVO->getUserId()) . ", journey=" . DBPostgres::checkNull($journeyHistoryVO->getJourney()) . ", end_date=" . DBPostgres::formatDate($journeyHistoryVO->getEndDate()) . " WHERE id=".$journeyHistoryVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_journey_history_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Journey History creator for PostgreSQL.
     *
     * This function creates a new row for a Journey History by its {@link JourneyHistoryVO}. The internal id of <var>$journeyHistoryVO</var> will be set after its creation.
     *
     * @param JourneyHistoryVO $journeyHistoryVO the {@link JourneyHistoryVO} with the data we wat to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(JourneyHistoryVO $journeyHistoryVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO journey_history (init_date, usrid, journey, end_date) VALUES (" . DBPostgres::formatDate($journeyHistoryVO->getInitDate()) . ", " . DBPostgres::checkNull($journeyHistoryVO->getUserId()) . ", " . DBPostgres::checkNull($journeyHistoryVO->getJourney()) . ", " . DBPostgres::formatDate($journeyHistoryVO->getEndDate()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_journey_history_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $journeyHistoryVO->setId(DBPostgres::getId($this->connect, "journey_history_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Journey History deleter for PostgreSQL.
     *
     * This function deletes the data of a Journey History by its {@link JourneyHistoryVO}.
     *
     * @param JourneyHistoryVO $journeyHistoryVO the {@link JourneyHistoryVO} with the data we wat to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(JourneyHistoryVO $journeyHistoryVO) {
        $affectedRows = 0;

        // Check for a journey history entry ID.
        if($journeyHistoryVO->getId() >= 0) {
            $currJourneyHistoryVO = $this->getById($journeyHistoryVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currJourneyHistoryVO) > 0) {
            $sql = "DELETE FROM journey_history WHERE id=".$journeyHistoryVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLJourneyHistoryDAO();

// We create a new journey history entry

$journeyHistory = new JourneyHistoryVO();

$journeyHistory->setInitDate(date_create('2005-12-21'));
$journeyHistory->setUserId(1);
$journeyHistory->setJourney(1);

$dao->create($journeyHistory);

print ("New journey history entry Id is ". $journeyHistory->getId() ."\n");

// We search for the new Id

$journeyHistory = $dao->getById($journeyHistory->getId());

print ("New journey history entry Id found is ". $journeyHistory->getId() ."\n");

print ("New journey history entry date found is ". DBPostgres::formatDate($journeyHistory->getInitDate()) ."\n");

// We update the journey history entry with a differente init date

$journeyHistory->setInitDate(date_create('2000-10-10'));

$dao->update($journeyHistory);

// We search for the new init date

$journeyHistory = $dao->getById($journeyHistory->getId());

print ("New journey history entry date found is ". DBPostgres::formatDate($journeyHistory->getInitDate()) ."\n");

// We delete the new journey history entry

$dao->delete($journeyHistory);*/
