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


/** File for ExtraHourDAO
 *
 *  This file just contains {@link ExtraHourDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/ExtraHourVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Extra Hours
 *
 *  This is the base class for all types of Extra Hour DAOs responsible for working with data from Extra Hour table, providing a common interface.
 *
 * @see DAOFactory::getExtraHourDAO(), ExtraHourVO
 */
abstract class ExtraHourDAO extends BaseDAO{

    /** Extra Hour DAO constructor.
     *
     * This is the base constructor of Extra Hour DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Extra Hour retriever by id.
     *
     * This function retrieves the row from Extra Hour table with the id <var>$extraHourId</var> and creates an {@link ExtraHourVO} with its data.
     *
     * @param int $extraHourId the id of the row we want to retrieve.
     * @return ExtraHourVO a value object {@link ExtraHourVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($extraHourId);

    /** Extra Hours retriever by User id.
     *
     * This function retrieves the rows from Extra Hour table that are associated with the User with
     * the id <var>$userId</var> and creates an {@link ExtraHourVO} with data from each row.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserId($userId);

    /** Extra Hour last entry retriever by User id and date.
     *
     * This function retrieves the latest row from Extra Hour table that is associated with the User with
     * the id <var>$userId</var> and has a date before <var>$nowadays</var> and creates an {@link ExtraHourVO} with its data.
     *
     * @param int $userId the id of the User whose Extra Hours we want to retrieve.
     * @param DateTime $nowadays the limit date for searching for the last entry before it.
     * @return ExtraHourVO a value object {@link ExtraHourVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getLastByUserId($userId, DateTime $nowadays);

    /** Extra Hours retriever.
     *
     * This function retrieves all rows from Extra Hour table and creates an {@link ExtraHourVO} with data from each row.
     *
     * @return array an array with value objects {@link ExtraHourVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Extra Hour updater.
     *
     * This function updates the data of a Extra Hour by its {@link ExtraHourVO}.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(ExtraHourVO $extraHourVO);

    /** Extra Hour creator.
     *
     * This function creates a new row for a Extra Hour by its {@link ExtraHourVO}.
     * The internal id of <var>$extraHourVO</var> will be set after its creation.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(ExtraHourVO $extraHourVO);

    /** Extra Hour deleter.
     *
     * This function deletes the data of a Extra Hour by its {@link ExtraHourVO}.
     *
     * @param ExtraHourVO $extraHourVO the {@link ExtraHourVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(ExtraHourVO $extraHourVO);

}
