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

    /** Sector value object constructor for PostgreSQL.
     *
     * This function creates a new {@link SectorVO} with data retrieved from database.
     *
     * @param array $row an array with the Sector values from a row.
     * @return SectorVO an {@link SectorVO} with its properties set to the values from <var>$row</var>.
     * @see SectorVO
     */
    protected function setValues($row)
    {

        $sectorVO = new SectorVO();

        $sectorVO->setId($row['id']);
        $sectorVO->setName($row['name']);

        return $sectorVO;

    }

    /** Sector retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Sector table with the id <var>$sectorId</var> and creates a {@link SectorVO} with its data.
     *
     * @param int $sectorId the id of the row we want to retrieve.
     * @return SectorVO a value object {@link SectorVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($sectorId) {
        if (!is_numeric($sectorId))
        throw new SQLIncorrectTypeException($sectorId);
        $sql = "SELECT * FROM sector WHERE id=" . $sectorId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Customers retriever by Sector id for PostgreSQL.
     *
     * This function retrieves the rows from Customer table that are assigned to the Sector with
     * the id <var>$sectorId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $sectorId the id of the Sector whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomerDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCustomers($sectorId) {

    $dao = DAOFactory::getCustomerDAO();
    return $dao->getBySectorId($sectorId);

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
        return $this->execute($sql);
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

        if($sectorVO->getId() >= 0) {
            $currsectorVO = $this->getById($sectorVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currsectorVO) > 0) {

            $sql = "UPDATE sector SET name=" . DBPostgres::checkStringNull($sectorVO->getName()) . " WHERE id=".$sectorVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_sector_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
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

        $sql = "INSERT INTO sector (name) VALUES (" . DBPostgres::checkStringNull($sectorVO->getName()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_sector_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $sectorVO->setId(DBPostgres::getId($this->connect, "sector_id_seq"));

        $affectedRows = pg_affected_rows($res);

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

        // Check for a sector ID.
        if($sectorVO->getId() >= 0) {
            $currsectorVO = $this->getById($sectorVO->getId());
        }

        // Delete a sector.
        if(sizeof($currsectorVO) > 0) {
            $sql = "DELETE FROM sector WHERE id=".$sectorVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLSectorDAO();

// We create a new sector

$sector = new sectorVO();

$sector->setName("Telenet");

$dao->create($sector);

print ("New sector Id is ". $sector->getId() ."\n");

// We search for the new Id

$sector = $dao->getById($sector->getId());

print ("New sector Id found is ". $sector->getId() ."\n");

// We update the sector with a differente name

$sector->setName("Intranet");

$dao->update($sector);

// We search for the new name

$sector = $dao->getById($sector->getId());

print ("New sector name found is ". $sector->getName() ."\n");

// We delete the new sector

$dao->delete($sector);*/
