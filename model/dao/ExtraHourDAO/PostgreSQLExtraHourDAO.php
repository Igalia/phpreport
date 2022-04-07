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

    /** Extra Hour retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Extra Hour table with the id <var>$extraHourId</var> and creates an {@link ExtraHourVO} with its data.
     *
     * @param int $extraHourId the id of the row we want to retrieve.
     * @return ExtraHourVO a value object {@link ExtraHourVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($extraHourId) {
        $result = $this->runSelectQuery(
            "SELECT * FROM extra_hour WHERE id=:extraHourId",
            [':extraHourId' => $extraHourId],
            'ExtraHourVO');
        return $result[0] ?? NULL;
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
        $result = $this->runSelectQuery(
            "SELECT * FROM extra_hour " .
              "WHERE usrid=:userId AND _date <= :nowadays " .
              "ORDER BY _date DESC LIMIT 1",
            [':userId' => $userId, ':nowadays' => DBPostgres::formatDate($nowadays)],
            'ExtraHourVO');
        return $result[0] ?? NULL;
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
        return $this->runSelectQuery($sql, [], 'ExtraHourVO');
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

        $sql = "UPDATE extra_hour " .
               "SET _date=:date, hours=:hours, usrid=:usrid, comment=:comment " .
               "WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":date", DBPostgres::formatDate($extraHourVO->getDate()), PDO::PARAM_STR);
            $statement->bindValue(":usrid", $extraHourVO->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(":comment", $extraHourVO->getComment(), PDO::PARAM_STR);
            $statement->bindValue(":id", $extraHourVO->getId(), PDO::PARAM_INT);
            // Notice there is no specific parameter for floating point values. A proposal
            // existed but did not land: https://wiki.php.net/rfc/pdo_float_type
            // The current recommended practice is to use PDO::PARAM_STR.
            $statement->bindValue(":hours", $extraHourVO->getHours(), PDO::PARAM_STR);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
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

        $sql = "INSERT INTO extra_hour (_date, hours, usrid, comment) " .
               "VALUES (:date, :hours, :usrid, :comment)";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":date", DBPostgres::formatDate($extraHourVO->getDate()), PDO::PARAM_STR);
            $statement->bindValue(":usrid", $extraHourVO->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(":comment", $extraHourVO->getComment(), PDO::PARAM_STR);
            // Notice there is no specific parameter for floating point values. A proposal
            // existed but did not land: https://wiki.php.net/rfc/pdo_float_type
            // The current recommended practice is to use PDO::PARAM_STR.
            $statement->bindValue(":hours", $extraHourVO->getHours(), PDO::PARAM_STR);
            $statement->execute();

            $extraHourVO->setId($this->pdo->lastInsertId('extra_hour_id_seq'));

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }
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

        $sql = "DELETE FROM extra_hour WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":id", $extraHourVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
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
