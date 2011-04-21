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


/** File for PostgreSQLCommonEventDAO
 *
 *  This file just contains {@link PostgreSQLCommonEventDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/CommonEventVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CommonEventDAO/CommonEventDAO.php');

/** DAO for Common Events in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link CommonEventDAO}.
 *
 * @see CommonEventDAO, CommonEventVO
 */
class PostgreSQLCommonEventDAO extends CommonEventDAO{

    /** Common Event DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link CommonEventDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see CommonEventDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Common Event value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CommonEventVO} with data retrieved from database.
     *
     * @param array $row an array with the Common Event values from a row.
     * @return CommonEventVO a {@link CommonEventVO} with its properties set to the values from <var>$row</var>.
     * @see CommonEventVO
     */
    protected function setValues($row)
    {

    $commonEventVO = new CommonEventVO();

        $commonEventVO->setId($row['id']);
        $commonEventVO->setDate(date_create($row['_date']));
    $commonEventVO->setCityId($row['cityid']);

    return $commonEventVO;
    }

    /** Common Event retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Common Event table with the id <var>$commonEventId</var> and creates a {@link CommonEventVO} with its data.
     *
     * @param int $commonEventId the id of the row we want to retrieve.
     * @return CommonEventVO a value object {@link CommonEventVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($commonEventId) {
        if (!is_numeric($commonEventId))
        throw new SQLIncorrectTypeException($commonEventId);
        $sql = "SELECT * FROM common_event WHERE id=" . $commonEventId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Common Events retriever by City id for PostgreSQL.
     *
     * This function retrieves the rows from Common Event table that are associated with the City with
     * the id <var>$cityId</var> and creates a {@link CommonEventVO} with data from each row.
     *
     * @param int $cityId the id of the City whose Common Events we want to retrieve.
     * @return array an array with value objects {@link CommonEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCityId($cityId) {
        $sql = "SELECT * FROM common_event WHERE cityid=" . $cityId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Common Events retriever by City id and date interval for PostgreSQL.
     *
     * This function retrieves the rows from Common Event table that are associated with the City with
     * the id <var>$cityId</var> and that lay between <var>$init</var> and <var>$end</var> dates and creates a {@link CommonEventVO} with data from that row.
     *
     * @param int $cityId the id of the City whose Common Event current entry we want to retrieve.
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval.
     * @return CommonEventVO a value object {@link CommonEventVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCityIdDates($cityId, DateTime $init, DateTime $end) {
        $sql = "SELECT * FROM common_event WHERE cityid=" . $cityId . " AND _date BETWEEN " . DBPostgres::formatDate($init) . " AND " . DBPostgres::formatDate($end) . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Common Events retriever for PostgreSQL.
     *
     * This function retrieves all rows from Common Event table and creates a {@link CommonEventVO} with data from each row.
     *
     * @return array an array with value objects {@link CommonEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM common_event ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Common Event updater for PostgreSQL.
     *
     * This function updates the data of a Common Event by its {@link CommonEventVO}.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(CommonEventVO $commonEventVO) {
        $affectedRows = 0;

        if($commonEventVO->getId() >= 0) {
            $currCommonEventVO = $this->getById($commonEventVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currCommonEventVO) > 0) {

            $sql = "UPDATE common_event SET _date=" . DBPostgres::formatDate($commonEventVO->getDate()) . ", cityid=" . DBPostgres::checkNull($commonEventVO->getCityId()) . " WHERE id=".$commonEventVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_common_event_city_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Common Event creator for PostgreSQL.
     *
     * This function creates a new row for a Common Event by its {@link CommonEventVO}.
     * The internal id of <var>$commonEventVO</var> will be set after its creation.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(CommonEventVO $commonEventVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO common_event (_date, cityid) VALUES (" . DBPostgres::formatDate($commonEventVO->getDate()) . ", " . DBPostgres::checkNull($commonEventVO->getCityId()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_common_event_city_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $commonEventVO->setId(DBPostgres::getId($this->connect, "common_event_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Common Event deleter for PostgreSQL.
     *
     * This function deletes the data of a Common Event by its {@link CommonEventVO}.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(CommonEventVO $commonEventVO) {
        $affectedRows = 0;

        // Check for a common event ID.
        if($commonEventVO->getId() >= 0) {
            $currCommonEventVO = $this->getById($commonEventVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currCommonEventVO) > 0) {
            $sql = "DELETE FROM common_event WHERE id=".$commonEventVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLcommonEventDAO();

// We create a new common event

$commonEvent = new commonEventVO();

$commonEvent->setDate(date_create('2005-12-21'));
$commonEvent->setCityId(1);

$dao->create($commonEvent);

print ("New common event Id is ". $commonEvent->getId() ."\n");

// We search for the new Id

$commonEvent = $dao->getById($commonEvent->getId());

print ("New common event Id found is ". $commonEvent->getId() ."\n");

// We update the common event with a differente date

$commonEvent->setDate(date_create('2000-10-10'));

$dao->update($commonEvent);

// We search for the new date

$commonEvent = $dao->getById($commonEvent->getId());

print ("New common event date found is ". DBPostgres::formatDate($commonEvent->getDate()) ."\n");

// We delete the new common event

$dao->delete($commonEvent);*/
