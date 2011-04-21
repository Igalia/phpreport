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


/** File for HourCostHistoryDAO
 *
 *  This file just contains {@link HourCostHistoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/HourCostHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Hour Cost Histories
 *
 *  This is the base class for all types of Hour Cost History DAOs responsible for working with data from Hour Cost History table, providing a common interface.
 *
 * @see DAOFactory::getHourCostHistoryDAO(), HourCostHistoryVO
 */
abstract class HourCostHistoryDAO extends BaseDAO{

    /** Hour Cost History DAO constructor.
     *
     * This is the base constructor of Hour Cost History DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Hour Cost History retriever by id.
     *
     * This function retrieves the row from Hour Cost History table with the id <var>$hourCostHistoryId</var> and creates a {@link HourCostHistoryVO} with its data.
     *
     * @param int $hourCostHistoryId the id of the row we want to retrieve.
     * @return HourCostHistoryVO a value object {@link HourCostHistoryVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($hourCostHistoryId);

    /** Hour Cost History retriever by User id.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link HourCostHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserId($userId);

    /** Hour Cost History current entry retriever by User id.
     *
     * This function retrieves the current row from Hour Cost History table that is associated with the User with
     * the id <var>$userId</var> and creates a {@link HourCostHistoryVO} with data from that row.
     *
     * @param int $userId the id of the User whose Hour Cost History current entry we want to retrieve.
     * @return HourCostHistoryVO a value object {@link HourCostHistoryVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getCurrentByUserId($userId);

    /** Hour Cost History retriever by User id and date interval.
     *
     * This function retrieves the rows from Hour Cost History table that are associated with the User with
     * the id <var>$userId</var> and that full lay inside the interval defined by <var>$init</var> and <var>$end</var>
     * and creates a {@link HourCostHistoryVO} with data from each row.
     * If we don't indicate a User id, then entries for all users are returned.
     *
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval (included).
     * @param int $userId the id of the User whose Hour Cost History we want to retrieve. It's optional.
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by the User database internal identifier and the beginning date.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByIntervals(DateTime $init, DateTime $end, $userId = NULL);

    /** Hour Cost Histories retriever.
     *
     * This function retrieves all rows from Hour Cost History table and creates a {@link HourCostHistoryVO} with data from each row.
     *
     * @return array an array with value objects {@link HourCostHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Hour Cost History updater.
     *
     * This function updates the data of a Hour Cost History by its {@link HourCostHistoryVO}.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(HourCostHistoryVO $hourCostHistoryVO);

    /** Hour Cost History creator.
     *
     * This function creates a new row for a Hour Cost History by its {@link HourCostHistoryVO}. The internal id of <var>$hourCostHistoryVO</var> will be set after its creation.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(HourCostHistoryVO $hourCostHistoryVO);

    /** Hour Cost History deleter.
     *
     * This function deletes the data of a Hour Cost History by its {@link HourCostHistoryVO}.
     *
     * @param HourCostHistoryVO $hourCostHistoryVO the {@link HourCostHistoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(HourCostHistoryVO $hourCostHistoryVO);

}
