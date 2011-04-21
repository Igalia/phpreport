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


/** File for PostgreSQLAreaHistoryDAO
 *
 *  This file just contains {@link PostgreSQLAreaHistoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaHistoryDAO/AreaHistoryDAO.php');

/** DAO for Area Histories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link AreaHistoryDAO}.
 *
 * @see AreaHistoryDAO, AreaHistoryVO
 */
class PostgreSQLAreaHistoryDAO extends AreaHistoryDAO{

    /** Area History DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link AreaHistoryDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see AreaHistoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Area History value object constructor for PostgreSQL.
     *
     * This function creates a new {@link AreaHistoryVO} with data retrieved from database.
     *
     * @param array $row an array with the Area History values from a row.
     * @return AreaHistoryVO an {@link AreaHistoryVO} with its properties set to the values from <var>$row</var>.
     * @see AreaHistoryVO
     */
    protected function setValues($row)
    {

    $areaHistoryVO = new AreaHistoryVO();

        $areaHistoryVO->setId($row['id']);
        $areaHistoryVO->setInitDate(date_create($row['init_date']));
    $areaHistoryVO->setUserId($row['usrid']);
    if (is_null($row['end_date']))
            $areaHistoryVO->setEndDate(NULL);
    else
        $areaHistoryVO->setEndDate(date_create($row['end_date']));
    $areaHistoryVO->setAreaId($row['areaid']);

    return $areaHistoryVO;
    }

   /** Area History retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Area History table with the id <var>$areaHistoryId</var> and creates an {@link AreaHistoryVO} with its data.
     *
     * @param int $areaHistoryId the id of the row we want to retrieve.
     * @return AreaHistoryVO a value object {@link AreaHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($areaHistoryId) {
    if (!is_numeric($areaHistoryId))
        throw new SQLIncorrectTypeException($areaHistoryId);
        $sql = "SELECT * FROM area_history WHERE id=" . $areaHistoryId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Area History retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Area History table that are associated with the User with
     * the id <var>$userId</var> and creates an {@link AreaHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Area History we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM area_history WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Current Area History entry retriever by User id for PostgreSQL.
     *
     * This function retrieves the current row from Area History table that is associated with the User with
     * the id <var>$userId</var> and creates an {@link AreaHistoryVO} with data from that row.
     *
     * @param int $userId the id of the User whose Area History current entry we want to retrieve.
     * @return AreaHistoryVO a value object {@link AreaHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getCurrentByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM area_history WHERE usrid=" . $userId . " AND end_date IS NULL";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Area History retriever by User id and date interval for PostgreSQL.
     *
     * This function retrieves the rows from Area History table that are associated with the User with
     * the id <var>$userId</var> and that lay inside the interval defined by <var>$init</var> and <var>$end</var>
     * and creates an {@link AreaHistoryVO} with data from each row.
     * If we don't indicate a User id, then entries for all users are returned.
     *
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval (included).
     * @param int $userId the id of the User whose Area History we want to retrieve. It's optional.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by the User database internal identifier and the beginning date.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByIntervals(DateTime $init, DateTime $end, $userId = NULL) {
    if (is_null($userId))
        $sql = "SELECT * FROM area_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL))
        ORDER BY usrid ASC, init_date ASC";
    elseif (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        else
        $sql = "SELECT * FROM area_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL) AND (usrId = $userId))
        ORDER BY usrid ASC, init_date ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Area History retriever by Area id for PostgreSQL.
     *
     * This function retrieves the rows from Area History table that are associated with the Area with
     * the id <var>$areaId</var> and creates an {@link AreaHistoryVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Area History associated entries we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByAreaId($areaId) {
        $sql = "SELECT * FROM area_history WHERE areaid=" . $areaId . " ORDER BY id ASC";
        $result = $this->execute($sql);
        return $result;
    }

    /** Area History retriever for PostgreSQL.
     *
     * This function retrieves all rows from Area History table and creates an {@link AreaHistoryVO} with data from each row.
     *
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM area_history ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Area History updater for PostgreSQL.
     *
     * This function updates the data of an Area History by its {@link AreaHistoryVO}.
     *
     * @param AreaHistoryVO $areaHistoryVO the {@link AreaHistoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(AreaHistoryVO $areaHistoryVO) {
        $affectedRows = 0;

        if($areaHistoryVO->getId() >= 0) {
            $currAreaHistoryVO = $this->getById($areaHistoryVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currAreaHistoryVO) > 0) {

            $sql = "UPDATE area_history SET init_date=" . DBPostgres::formatDate($areaHistoryVO->getInitDate()) . ", usrid=" . DBPostgres::checkNull($areaHistoryVO->getUserId()) . ", areaid=" . DBPostgres::checkNull($areaHistoryVO->getAreaId()) . ", end_date=" . DBPostgres::formatDate($areaHistoryVO->getEndDate()) . " WHERE id=".$areaHistoryVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_area_history_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Area History creator for PostgreSQL.
     *
     * This function creates a new row for an Area History by its {@link AreaHistoryVO}. The internal id of <var>$areaHistoryVO</var> will be set after its creation.
     *
     * @param AreaHistoryVO $areaHistoryVO the {@link AreaHistoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(AreaHistoryVO $areaHistoryVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO area_history (init_date, usrid, areaid, end_date) VALUES (" . DBPostgres::formatDate($areaHistoryVO->getInitDate()) . ", " . DBPostgres::checkNull($areaHistoryVO->getUserId()) . ", " . DBPostgres::checkNull($areaHistoryVO->getAreaId()) . ", " . DBPostgres::formatDate($areaHistoryVO->getEndDate()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_area_history_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $areaHistoryVO->setId(DBPostgres::getId($this->connect, "area_history_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Area History deleter for PostgreSQL.
     *
     * This function deletes the data of an Area History by its {@link AreaHistoryVO}.
     *
     * @param AreaHistoryVO $areaHistoryVO the {@link AreaHistoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(AreaHistoryVO $areaHistoryVO) {
        $affectedRows = 0;

        // Check for a area history entry ID.
        if($areaHistoryVO->getId() >= 0) {
            $currAreaHistoryVO = $this->getById($areaHistoryVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currAreaHistoryVO) > 0) {
            $sql = "DELETE FROM area_history WHERE id=".$areaHistoryVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLareaHistoryDAO();

// We create a new area history entry

$areaHistory = new areaHistoryVO();

$areaHistory->setInitDate(date_create('2005-12-21'));
$areaHistory->setUserId(1);
$areaHistory->setAreaId(1);

$dao->create($areaHistory);

print ("New area history entry Id is ". $areaHistory->getId() ."\n");

// We search for the new Id

$areaHistory = $dao->getById($areaHistory->getId());

print ("New area history entry Id found is ". $areaHistory->getId() ."\n");

// We update the area history entry with a differente init date

$areaHistory->setInitDate(date_create('2000-10-10'));

$dao->update($areaHistory);

// We search for the new init date

$areaHistory = $dao->getById($areaHistory->getId());

print ("New area history entry date found is ". DBPostgres::formatDate($areaHistory->getInitDate()) ."\n");

// We delete the new area history entry

$dao->delete($areaHistory);*/
