<?php

/** File for SectorDAO
 *
 *  This file just contains {@link SectorDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/SectorVO.php');
include_once('phpreport/model/dao/BaseDAO.php');

/** DAO for Sectors
 *
 *  This is the base class for all types of Sector DAOs responsible for working with data from Sector table, providing a common interface.
 *
 * @see DAOFactory::getSectorDAO(), SectorVO
 */
abstract class SectorDAO extends BaseDAO{

    /** Sector DAO constructor.
     *
     * This is the base constructor of Sector DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Sector retriever by id.
     *
     * This function retrieves the row from Sector table with the id <var>$sectorId</var> and creates a {@link SectorVO} with its data.
     *
     * @param int $sectorId the id of the row we want to retrieve.
     * @return SectorVO a value object {@link SectorVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($sectorId);

    /** Customers retriever by Sector id.
     *
     * This function retrieves the rows from Customer table that are assigned to the Sector with
     * the id <var>$sectorId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $sectorId the id of the Sector whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomerDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getCustomers($sectorId);

    /** Sectors retriever.
     *
     * This function retrieves all rows from Sector table and creates a {@link SectorVO} with data from each row.
     *
     * @return array an array with value objects {@link SectorVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Sector updater.
     *
     * This function updates the data of a Sector by its {@link SectorVO}.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(SectorVO $sectorVO);

    /** Sector creator.
     *
     * This function creates a new row for a Sector by its {@link SectorVO}. The internal id of <var>$sectorVO</var> will be set after its creation.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(SectorVO $sectorVO);

    /** Sector deleter.
     *
     * This function deletes the data of a Sector by its {@link SectorVO}.
     *
     * @param SectorVO $sectorVO the {@link SectorVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(SectorVO $sectorVO);

}
