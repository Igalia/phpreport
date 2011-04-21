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


/** File for CommonEventDAO
 *
 *  This file just contains {@link CommonEventDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/CommonEventVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Common Events
 *
 *  This is the base class for all types of Common Event DAOs responsible for working with data from Common Event table, providing a common interface.
 *
 * @see DAOFactory::getCommonEventDAO(), CommonEventVO
 */
abstract class CommonEventDAO extends BaseDAO{

    /** Common Event DAO constructor.
     *
     * This is the base constructor of Common Event DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Common Event retriever by id.
     *
     * This function retrieves the row from Common Event table with the id <var>$commonEventId</var> and creates a {@link CommonEventVO} with its data.
     *
     * @param int $commonEventId the id of the row we want to retrieve.
     * @return CommonEventVO a value object {@link CommonEventVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($commonEventId);

    /** Common Events retriever by City id.
     *
     * This function retrieves the rows from Common Event table that are associated with the City with
     * the id <var>$cityId</var> and creates a {@link CommonEventVO} with data from each row.
     *
     * @param int $cityId the id of the City whose Common Events we want to retrieve.
     * @return array an array with value objects {@link CommonEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByCityId($cityId);

    /** Common Events retriever by City id and date interval.
     *
     * This function retrieves the rows from Common Event table that are associated with the City with
     * the id <var>$cityId</var> and that lay between <var>$init</var> and <var>$end</var> dates and creates a {@link CommonEventVO} with data from that row.
     *
     * @param int $cityId the id of the City whose Common Event current entry we want to retrieve.
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval.
     * @return CommonEventVO a value object {@link CommonEventVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByCityIdDates($cityId, DateTime $init, DateTime $end);

    /** Common Events retriever.
     *
     * This function retrieves all rows from Common Event table and creates a {@link CommonEventVO} with data from each row.
     *
     * @return array an array with value objects {@link CommonEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Common Event updater.
     *
     * This function updates the data of a Common Event by its {@link CommonEventVO}.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(CommonEventVO $commonEventVO);

    /** Common Event creator.
     *
     * This function creates a new row for a Common Event by its {@link CommonEventVO}.
     * The internal id of <var>$commonEventVO</var> will be set after its creation.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(CommonEventVO $commonEventVO);

    /** Common Event deleter.
     *
     * This function deletes the data of a Common Event by its {@link CommonEventVO}.
     *
     * @param CommonEventVO $commonEventVO the {@link CommonEventVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(CommonEventVO $commonEventVO);

}
