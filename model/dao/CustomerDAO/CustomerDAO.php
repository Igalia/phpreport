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


/** File for CustomerDAO
 *
 *  This file just contains {@link CustomerDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Customers
 *
 *  This is the base class for all types of Customer DAOs responsible for working with data from Customer table, providing a common interface.
 *
 * @see DAOFactory::getCustomerDAO(), CustomerVO
 */
abstract class CustomerDAO extends BaseDAO{

    /** Customer DAO constructor.
     *
     * This is the base constructor of Customer DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Customer retriever by id.
     *
     * This function retrieves the row from Customer table with the id <var>$customerId</var> and creates a {@link CustomerVO} with its data.
     *
     * @param int $customerId the id of the row we want to retrieve.
     * @return CustomerVO a value object {@link CustomerVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($customerId);

    /** Customers retriever by Sector id.
     *
     * This function retrieves the rows from Customer table that are assigned to the Sector with
     * the id <var>$sectorId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $sectorId the id of the Sector whose Customers we want to retrieve.
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see SectorDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getBySectorId($sectorId, $orderField = 'id');

    /** Customers retriever by projects done by a User identified by its login.
     *
     * This function retrieves the rows from Customer table that are related to Projects
     * done by a User.
     *
     * @param string $login the login of the User whose Projects' Customers we want to retrieve.
     * @param bool $active optional parameter for obtaining only data related to active Projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByProjectUserLogin($userLogin, $active = False, $orderField = 'id');

    /** Tasks retriever by Customer id.
     *
     * This function retrieves the rows from Task table that are assigned to the Customer with
     * the id <var>$customerId</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $customerId the id of the Customer whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getTasks($customerId);

    /** Projects retriever by Customer id.
     *
     * This function retrieves the rows from Project table that are assigned through relationship Requests to the Customer with
     * the id <var>$customerId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see RequestsDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getProjects($customerId, $active = False);

    /** Requests relationship entry creator by Customer id and Project id.
     *
     * This function creates a new entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the Customer.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function addProject($customerId, $projectId);

    /** Requests relationship entry deleter by Customer id and Project id.
     *
     * This function deletes an entry in the table Requests (that represents that relationship between Projects and Customers)
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the Customer we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see RequestsDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function removeProject($customerId, $projectId);

    /** Customer retriever.
     *
     * This function retrieves all rows from Customer table and creates a {@link CustomerVO} with data from each row.
     *
     * @param bool $active optional parameter for obtaining only data related to active Projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll($active = False, $orderField = 'id');

    /** Customer updater.
     *
     * This function updates the data of a Customer by its {@link CustomerVO}.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(CustomerVO $customerVO);

    /** Customer creator.
     *
     * This function creates a new row for a Customer by its {@link CustomerVO}. The internal id of <var>$customerVO</var> will be set after its creation.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(CustomerVO $customerVO);

    /** Customer deleter.
     *
     * This function deletes the data of a Customer by its {@link CustomerVO}.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(CustomerVO $customerVO);

}
