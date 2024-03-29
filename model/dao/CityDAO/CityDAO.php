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


/** File for CityDAO
 *
 *  This file just contains {@link CityDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/CityVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Cities
 *
 *  This is the base class for all types of City DAOs responsible for working with data from City table, providing a common interface.
 *
 * @see DAOFactory::getCityDAO(), CityVO
 */
abstract class CityDAO extends BaseDAO {

    /** Cities retriever.
     *
     * This function retrieves all rows from City table and creates a {@link CityVO} with data from each row.
     *
     * @return array an array with value objects {@link CityVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** City updater.
     *
     * This function updates the data of a City by its {@link CityVO}.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(CityVO $cityVO);

    /** City creator.
     *
     * This function creates a new row for a City by its {@link CityVO}. The internal id of <var>$cityVO</var> will be set after its creation.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(CityVO $cityVO);

    /** City deleter.
     *
     * This function deletes the data of a City by its {@link CityVO}.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(CityVO $cityVO);

}
