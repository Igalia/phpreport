<?php

/** File for AreaDAO
 *
 *  This file just contains {@link AreaDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/AreaVO.php');
include_once('phpreport/model/dao/BaseDAO.php');

/** DAO for Areas
 *
 *  This is the base class for all types of Area DAOs responsible for working with data from Area table, providing a common interface.
 *
 * @see DAOFactory::getAreaDAO(), AreaVO
 */
abstract class AreaDAO extends BaseDAO{

    /** Area DAO constructor.
     *
     * This is the base constructor of Area DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Area retriever by id.
     *
     * This function retrieves the row from Area table with the id <var>$areaId</var> and creates an {@link AreaVO} with its data.
     *
     * @param int $areaId the id of the row we want to retrieve.
     * @return AreaVO a value object {@link AreaVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getById($areaId);

    /** Area retriever by name for PostgreSQL.
     *
     * This function retrieves the row from Area table with the name <var>$areaName</var> and creates an {@link AreaVO} with its data.
     *
     * @param string $areaName the name of the row we want to retrieve.
     * @return AreaVO a value object {@link AreaVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByName($areaName);

    /** Projects retriever by Area id.
     *
     * This function retrieves the rows from Project table that are assigned to the Area with
     * the id <var>$areaId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getProjects($areaId);

    /** Area Histories retriever by Area id.
     *
     * This function retrieves the rows from Area History table that are assigned to the Area with
     * the id <var>$areaId</var> and creates an {@link AreaHistoryVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Area Histories we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see AreaHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getAreaHistories($areaId);

    /** Area retriever.
     *
     * This function retrieves all rows from Area table and creates an {@link AreaVO} with data from each row.
     *
     * @return array an array with value objects {@link AreaVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getAll();

    /** Area updater.
     *
     * This function updates the data of an Area by its {@link AreaVO}.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(AreaVO $areaVO);

    /** Area creator.
     *
     * This function creates a new row for an Area by its {@link AreaVO}. The internal id of <var>$areaVO</var> will be set after its creation.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(AreaVO $areaVO);

    /** Area deleter.
     *
     * This function deletes the data of an Area by its {@link AreaVO}.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLQueryErrorException}
     */
    public abstract function delete(AreaVO $areaVO);

}
