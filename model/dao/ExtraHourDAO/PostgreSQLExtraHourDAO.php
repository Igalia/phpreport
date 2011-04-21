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


/** File for PostgreSQLExtraHourDAO
 *
 *  This file just contains {@link PostgreSQLExtraHourDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/ExtraHourVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ExtraHourDAO/ExtraHourDAO.php');

/** DAO for Extra Hours in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link ExtraHourDAO}.
 *
 * @see ExtraHourDAO, ExtraHourVO
 */
class PostgreSQLExtraHourDAO extends ExtraHourDAO{

    /** Extra Hour DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link ExtraHourDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see ExtraHourDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Extra Hour value object constructor for PostgreSQL.
     *
     * This function creates a new {@link ExtraHourVO} with data retrieved from database.
     *
     * @param array $row an array with the Extra Hour values from a row.
     * @return ExtraHourVO an {@link ExtraHourVO} with its properties set to the values from <var>$row</var>.
     * @see ExtraHourVO
     */
    protected function setValues($row)
    {

        $extraHourVO = new ExtraHourVO();

        $extraHourVO->setId($row['id']);
        $extraHourVO->setDate(date_create($row['_date']));
        $extraHourVO->setHours($row['hours']);
        $extraHourVO->setUserId($row['usrid']);

    return $extraHourVO;
    }

    /** Extra Hour retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Extra Hour table with the id <var>$extraHourId</var> and creates an {@link ExtraHourVO} with its data.
     *
     * @param int $extraHourId the id of the row we want to retrieve.
     * @return ExtraHourVO a value object {@link ExtraHourVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($extraHourId) {
        if (!is_numeric($extraHourId))
        throw new SQLIncorrectTypeException($extraHourId);
        $sql = "SELECT * FROM extra_hour WHERE id=".$extraHourId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Extra Hours retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from Extra Hour table that are associated with the User with
     * the id <var>$userId</var> and creates an {@link ExtraHourVO} with data from each row.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
        if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM extra_hour WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Extra Hour last entry retriever by User id and date for PostgreSQL.
     *
     * This function retrieves the latest row from Extra Hour table that is associated with the User with
     * the id <var>$userId</var> and has a date before <var>$nowadays</var> and creates an {@link ExtraHourVO} with its data.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @param DateTime $nowadays the limit date for searching for the last entry before it.
     * @return ExtraHourVO a value object {@link ExtraHourVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getLastByUserId($userId, DateTime $nowadays) {
        if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM extra_hour WHERE usrid=" . $userId . " AND _date <=" . DBPostgres::formatDate($nowadays) . " ORDER BY _date DESC LIMIT 1";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Extra Hours retriever for PostgreSQL.
     *
     * This function retrieves all rows from Extra Hour table and creates an {@link ExtraHourVO} with data from each row.
     *
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM extra_hour ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Extra Hour updater for PostgreSQL.
     *
     * This function updates the data of a Extra Hour by its {@link ExtraHourVO}.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(ExtraHourVO $extraHourVO) {
    $affectedRows = 0;

        if($extraHourVO->getId() != "") {
            $currExtraHourVO = $this->getById($extraHourVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currExtraHourVO) > 0) {

            $sql = "UPDATE extra_hour SET _date=" .DBPostgres::formatDate($extraHourVO->getDate()) . ", hours="  . DBPostgres::checkNull($extraHourVO->getHours()) . ", usrid="  . DBPostgres::checkNull($extraHourVO->getUserId()) . " WHERE id=".$extraHourVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_extra_hour_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Extra Hour creator for PostgreSQL.
     *
     * This function creates a new row for a Extra Hour by its {@link ExtraHourVO}.
     * The internal id of <var>$extraHourVO</var> will be set after its creation.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(ExtraHourVO $extraHourVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO extra_hour (_date, hours, usrid) VALUES(" . DBPostgres::formatDate($extraHourVO->getDate()) . ", "  . DBPostgres::checkNull($extraHourVO->getHours()) . ","  . DBPostgres::checkNull($extraHourVO->getUserId()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_extra_hour_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $extraHourVO->setId(DBPostgres::getId($this->connect, "extra_hour_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Extra Hour deleter for PostgreSQL.
     *
     * This function deletes the data of a Extra Hour by its {@link ExtraHourVO}.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(ExtraHourVO $extraHourVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($extraHourVO->getId() >= 0) {
            $currExtraHourVO = $this->getById($extraHourVO->getId());
        }

        // Otherwise delete a user.
        if(sizeof($currExtraHourVO) > 0) {
            $sql = "DELETE FROM extra_hour WHERE id=".$extraHourVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLExtraHourDAO();

// We create a new extra hour

$extraHour = new ExtraHourVO();

$extraHour->setDate(date_create('2005-12-21'));
$extraHour->setHours(3);
$extraHour->setUserId(1);

$dao->create($extraHour);

print ("New extra hour Id is ". $extraHour->getId() ."\n");

// We search for the new Id

$extraHour = $dao->getById($extraHour->getId());

print ("New extra hour Id found is ". $extraHour->getId() ."\n");

print ("New extra hour Date found is ". DBPostgres::formatDate($extraHour->getDate()) ."\n");

// We update the extra hour with a differente number of hours

$extraHour->setHours(5);

$dao->update($extraHour);

// We search for the new value of hours

$extraHour = $dao->getById($extraHour->getId());

print ("New extra hour hours value found is ". $extraHour->getHours() ."\n");

// We delete the new extra hour

$dao->delete($extraHour);*/
