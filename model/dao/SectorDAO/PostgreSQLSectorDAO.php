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


/** File for PostgreSQLSectorDAO
 *
 *  This file just contains {@link PostgreSQLSectorDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectorVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectorDAO/SectorDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/PostgreSQLCustomerDAO.php');

/** DAO for Sectors in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link SectorDAO}.
 *
 * @see SectorDAO, SectorVO
 */
class PostgreSQLSectorDAO extends SectorDAO{

    /** Sector DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link SectorDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see SectorDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * This method is declared to fulfill this class as non-abstract, but it should not be used.
     * PDO::FETCH_CLASS now takes care of transforming DB rows into VO objects.
     */
    protected function setValues($row)
    {
        error_log("Unused SectorDAO::setValues() called");
    }

    /** Sector retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Sector table with the id
     * <var>$sectorId</var> and creates a {@link SectorVO} with its data.
     *
     * @param int $sectorId the id of the row we want to retrieve.
     * @return SectorVO a value object {@link SectorVO} with its properties set
     * to the values from the row, or NULL if no object was found for that id.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($sectorId) {
        if (!is_numeric($sectorId))
            throw new SQLIncorrectTypeException($customerId);
        $result = $this->runSelectQuery(
            "SELECT * FROM sector WHERE id=:sectorId",
            [':sectorId' => $sectorId],
            'SectorVO');
        return $result[0] ?? NULL;
    }

     /** Sectors retriever for PostgreSQL.
     *
     * This function retrieves all rows from Sector table and creates a {@link SectorVO} with data from each row.
     *
     * @return array an array with value objects {@link SectorVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM sector ORDER BY id ASC";
        return $this->runSelectQuery($sql, array(), 'SectorVO');
    }

    /** Sector updater for PostgreSQL.
     *
     * This function updates the data of a Sector by its {@link SectorVO}.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(SectorVO $sectorVO) {
        $affectedRows = 0;

        $sql = "UPDATE sector SET name=:name WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":name", $sectorVO->getName(), PDO::PARAM_STR);
            $statement->bindValue(":id", $sectorVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;
    }

    /** Sector creator for PostgreSQL.
     *
     * This function creates a new row for a Sector by its {@link SectorVO}. The internal id of <var>$sectorVO</var> will be set after its creation.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(SectorVO $sectorVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO sector (name) VALUES (:name)";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":name", $sectorVO->getName(), PDO::PARAM_STR);
            $statement->execute();

            $sectorVO->setId($this->pdo->lastInsertId('sector_id_seq'));

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;

    }

    /** Sector deleter for PostgreSQL.
     *
     * This function deletes the data of a Sector by its {@link SectorVO}.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(SectorVO $sectorVO) {
        $affectedRows = 0;

        $sql = "DELETE FROM sector WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":id", $sectorVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;
    }
}
