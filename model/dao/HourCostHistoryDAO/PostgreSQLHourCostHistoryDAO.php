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


/** File for PostgreSQLHourCostHistoryDAO
 *
 *  This file just contains {@link PostgreSQLHourCostHistoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/HourCostHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/HourCostHistoryDAO/HourCostHistoryDAO.php');

/** DAO for Hour Cost Histories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link HourCostHistoryDAO}.
 *
 * @see HourCostHistoryDAO, HourCostHistoryVO
 */
class PostgreSQLHourCostHistoryDAO extends HourCostHistoryDAO{

    /** Hour Cost History DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link HourCostHistoryDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see HourCostHistoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Hour Cost History value object constructor for PostgreSQL.
     *
     * This function creates a new {@link HourCostHistoryVO} with data retrieved from database.
     *
     * @param array $row an array with the Hour Cost History values from a row.
     * @return HourCostHistoryVO an {@link HourCostHistoryVO} with its properties set to the values from <var>$row</var>.
     * @see HourCostHistoryVO
     */
    protected function setValues($row)
    {

        $hourCostHistoryVO = new HourCostHistoryVO();

        $hourCostHistoryVO->setId($row['id']);
        $hourCostHistoryVO->setInitDate(date_create($row['init_date']));
        $hourCostHistoryVO->setUserId($row['usrid']);
        if (is_null($row['end_date']))
            $hourCostHistoryVO->setEndDate(NULL);
        else
            $hourCostHistoryVO->setEndDate(date_create($row['end_date']));
        $hourCostHistoryVO->setHourCost($row['hour_cost']);

        return $hourCostHistoryVO;
    }

    /** Hour Cost History retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Hour Cost History table with the id <var>$hourCostHistoryId</var> and creates an {@link HourCostHistoryVO} with its data.
     *
     * @param int $hourCostHistoryId the id of the row we want to retrieve.
     * @return HourCostHistoryVO a value object {@link HourCostHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($hourCostHistoryId) {
    if (!is_numeric($hourCostHistoryId))
        throw new SQLIncorrectTypeException($hourCostHistoryId);
        $sql = "SELECT * FROM hour_cost_history WHERE id=" . $hourCostHistoryId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Hour Cost History retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and creates an {@link HourCostHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM hour_cost_history WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Hour Cost History current entry retriever by User id for PostgreSQL.
     *
     * This function retrieves the current row from Hour Cost History table that is associated with the User with
     * the id <var>$userId</var> and creates an {@link HourCostHistoryVO} with data from that row.
     *
     * @param int $userId the id of the User whose Hour Cost History current entry we want to retrieve.
     * @return HourCostHistoryVO a value object {@link HourCostHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getCurrentByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM hour_cost_history WHERE usrid=" . $userId . " AND end_date IS NULL";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Hour Cost History retriever by User id and date interval for PostgreSQL.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and that full lay inside the interval defined by <var>$init</var> and <var>$end</var>
     * and creates an {@link HourCostHistoryVO} with data from each row.
     * If we don't indicate a User id, then entries for all users are returned.
     *
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval (included).
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve. It's optional.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by the User database internal identifier and the beginning date.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByIntervals(DateTime $init, DateTime $end, $userId = NULL) {
    if (is_null($userId))
        $sql = "SELECT * FROM hour_cost_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL))
        ORDER BY usrid ASC, init_date ASC";
    elseif (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        else
        $sql = "SELECT * FROM hour_cost_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL) AND (usrId = $userId))
        ORDER BY usrid ASC, init_date ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Hour Cost Histories retriever for PostgreSQL.
     *
     * This function retrieves all rows from Hour Cost History table and creates an {@link HourCostHistoryVO} with data from each row.
     *
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM hour_cost_history ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Hour Cost History updater for PostgreSQL.
     *
     * This function updates the data of an Hour Cost History by its {@link HourCostHistoryVO}.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(HourCostHistoryVO $hourCostHistoryVO) {
        $affectedRows = 0;

        if($hourCostHistoryVO->getId() >= 0) {
            $currHourCostHistoryVO = $this->getById($hourCostHistoryVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currHourCostHistoryVO) > 0) {

            $sql = "UPDATE hour_cost_history SET init_date=" . DBPostgres::formatDate($hourCostHistoryVO->getInitDate()) . ", usrid=" . DBPostgres::checkNull($hourCostHistoryVO->getUserId()) . ", hour_cost=" . DBPostgres::checkNull($hourCostHistoryVO->getHourCost()) . ", end_date=" . DBPostgres::formatDate($hourCostHistoryVO->getEndDate()) . " WHERE id=".$hourCostHistoryVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_hour_cost_history_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Hour Cost History creator for PostgreSQL.
     *
     * This function creates a new row for an Hour Cost History by its {@link HourCostHistoryVO}. The internal id of <var>$hourCostHistoryVO</var> will be set after its creation.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(HourCostHistoryVO $hourCostHistoryVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO hour_cost_history (init_date, usrid, hour_cost, end_date) VALUES (" . DBPostgres::formatDate($hourCostHistoryVO->getInitDate()) . ", " . DBPostgres::checkNull($hourCostHistoryVO->getUserId()) . ", " . DBPostgres::checkNull($hourCostHistoryVO->getHourCost()) . ", " . DBPostgres::formatDate($hourCostHistoryVO->getEndDate()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_hour_cost_history_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $hourCostHistoryVO->setId(DBPostgres::getId($this->connect, "hour_cost_history_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Hour Cost History deleter for PostgreSQL.
     *
     * This function deletes the data of an Hour Cost History by its {@link HourCostHistoryVO}.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(HourCostHistoryVO $hourCostHistoryVO) {
        $affectedRows = 0;

        // Check for a hour cost history entry ID.
        if($hourCostHistoryVO->getId() >= 0) {
            $currHourCostHistoryVO = $this->getById($hourCostHistoryVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currHourCostHistoryVO) > 0) {
            $sql = "DELETE FROM hour_cost_history WHERE id=".$hourCostHistoryVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLhourCostHistoryDAO();

// We create a new hour cost history entry

$hourCostHistory = new hourCostHistoryVO();

$hourCostHistory->setInitDate(date_create('2005-12-21'));
$hourCostHistory->setUserId(1);
$hourCostHistory->setHourCost(2.34);

$dao->create($hourCostHistory);

print ("New hour cost history entry Id is ". $hourCostHistory->getId() ."\n");

// We search for the new Id

$hourCostHistory = $dao->getById($hourCostHistory->getId());

print ("New hour cost history entry Id found is ". $hourCostHistory->getId() ."\n");

// We update the hour cost history entry with a differente init date

$hourCostHistory->setInitDate(date_create('2000-10-10'));

$dao->update($hourCostHistory);

// We search for the new init date

$hourCostHistory = $dao->getById($hourCostHistory->getId());

print ("New hour cost history entry date found is ". DBPostgres::formatDate($hourCostHistory->getInitDate()) ."\n");

// We delete the new hour cost history entry

$dao->delete($hourCostHistory);*/
