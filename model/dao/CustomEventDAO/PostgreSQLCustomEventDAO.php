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


/** File for PostgreSQLCustomEventDAO
 *
 *  This file just contains {@link PostgreSQLCustomEventDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomEventVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CustomEventDAO/CustomEventDAO.php');

/** DAO for Custom Events in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link CustomEventDAO}.
 *
 * @see CustomEventDAO, CustomEventVO
 */
class PostgreSQLCustomEventDAO extends CustomEventDAO{

    /** Custom Event DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link CustomEventDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see CustomEventDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Custom Event value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CustomEventVO} with data retrieved from database.
     *
     * @param array $row an array with the Custom Event values from a row.
     * @return CustomEventVO a {@link CustomEventVO} with its properties set to the values from <var>$row</var>.
     * @see CustomEventVO
     */
    protected function setValues($row)
    {

    $customEventVO = new CustomEventVO();

        $customEventVO->setId($row[id]);
        $customEventVO->setDate(date_create($row[_date]));
    $customEventVO->setUserId($row[usrid]);
    $customEventVO->setHours($row[hours]);
    $customEventVO->setType($row[type]);

    return $customEventVO;
    }

    /** Custom Event retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Custom Event table with the id <var>$customEventId</var> and creates a {@link CustomEventVO} with its data.
     *
     * @param int $customEventId the id of the row we want to retrieve.
     * @return CustomEventVO a value object {@link CustomEventVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($customEventId) {
        if (!is_numeric($customEventId))
        throw new SQLIncorrectTypeException($customEventId);
        $sql = "SELECT * FROM custom_event WHERE id=" . $customEventId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Custom Events retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Custom Event table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CustomEventVO} with data from each row.
     *
     * @param int $userId the id of the User whose Custom Events we want to retrieve.
     * @return array an array with value objects {@link CustomEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
        $sql = "SELECT * FROM custom_event WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Custom Events retriever for PostgreSQL.
     *
     * This function retrieves all rows from Custom Event table and creates a {@link CustomEventVO} with data from each row.
     *
     * @return array an array with value objects {@link CustomEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM custom_event ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Custom Event updater for PostgreSQL.
     *
     * This function updates the data of a Custom Event by its {@link CustomEventVO}.
     *
     * @param CustomEventVO $customEventVO the {@link CustomEventVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(CustomEventVO $customEventVO) {
        $affectedRows = 0;

        if($customEventVO->getId() >= 0) {
            $currCustomEventVO = $this->getById($customEventVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currCustomEventVO) > 0) {

            $sql = "UPDATE custom_event SET _date=" . DBPostgres::formatDate($customEventVO->getDate()) . ", usrid=" . DBPostgres::checkNull($customEventVO->getUserId()) . ", hours=" . DBPostgres::checkNull($customEventVO->getHours()) . ", type=" . DBPostgres::checkStringNull($customEventVO->getType()) . " WHERE id=".$customEventVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_extra_hour_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Custom Event creator for PostgreSQL.
     *
     * This function creates a new row for a Custom Event by its {@link CustomEventVO}.
     * The internal id of <var>$customEventVO</var> will be set after its creation.
     *
     * @param CustomEventVO $customEventVO the {@link CustomEventVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(CustomEventVO $customEventVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO custom_event (_date, usrid, hours, type) VALUES (" . DBPostgres::formatDate($customEventVO->getDate()) . ", " . DBPostgres::checkNull($customEventVO->getUserId()) . ", " . DBPostgres::checkNull($customEventVO->getHours()) . ", " . DBPostgres::checkStringNull($customEventVO->getType()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_custom_event_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $customEventVO->setId(DBPostgres::getId($this->connect, "custom_event_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Custom Event deleter for PostgreSQL.
     *
     * This function deletes the data of a Custom Event by its {@link CustomEventVO}.
     *
     * @param CustomEventVO $customEventVO the {@link CustomEventVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(CustomEventVO $customEventVO) {
        $affectedRows = 0;

        // Check for a custom event ID.
        if($customEventVO->getId() >= 0) {
            $currCustomEventVO = $this->getById($customEventVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currCustomEventVO) > 0) {
            $sql = "DELETE FROM custom_event WHERE id=".$customEventVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLcustomEventDAO();

// We create a new custom event

$customEvent = new customEventVO();

$customEvent->setDate(date_create('2005-12-21'));
$customEvent->setUserId(1);
$customEvent->setHours(3);
$customEvent->setType("Ctrl+Alt+Supr");

$dao->create($customEvent);

print ("New custom event Id is ". $customEvent->getId() ."\n");

// We search for the new Id

$customEvent = $dao->getById($customEvent->getId());

print ("New custom event Id found is ". $customEvent->getId() ."\n");

// We update the custom event with a differente date

$customEvent->setDate(date_create('2000-10-10'));

$dao->update($customEvent);

// We search for the new date

$customEvent = $dao->getById($customEvent->getId());

print ("New custom event date found is ". DBPostgres::formatDate($customEvent->getDate()) ."\n");

// We delete the new custom event

$dao->delete($customEvent);*/
