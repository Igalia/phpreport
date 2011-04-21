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


/** File for RequestsDAO
 *
 *  This file just contains {@link RequestsDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/BaseRelationshipDAO.php');

/** DAO for relationship Requests
 *
 *  This is the base class for all types of relationship Requests DAOs responsible for working with data from tables related to that relationship (Customer, Project and Requests), providing a common interface. <br><br>Its edges are:
 * - A: Customer
 * - B: Project
 *
 * @see DAOFactory::getRequestsDAO(), CustomerDAO, CustomerGroupDAO, CustomerVO, CustomerGroupVO
 */
abstract class RequestsDAO extends BaseRelationshipDAO{

    /** Requests DAO constructor.
     *
     * This is the base constructor of Requests DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Requests entry retriever by id's.
     *
     * This function retrieves the row from Requests table with the id's <var>$customerId</var> and <var>$projectId</var>.
     *
     * @param int $customerId the id (that matches with a Customer) of the row we want to retrieve.
     * @param int $projectId the id (that matches with a Project) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link OperationErrorException}
     */
    protected abstract function getByIds($customerId, $projectId);

    /** Projects retriever by Customer id.
     *
     * This function retrieves the rows from Project table that are assigned through relationship Requests to the Customer with
     * the id <var>$customerId</var> and creates a {@link ProjectVO} with data from each row. We can retrieve only active projects or all them.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomerDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByCustomerId($customerId, $active = False);

    /** Customers retriever by Project id.
     *
     * This function retrieves the rows from Customer table that are assigned through relationship Requests to the Project with
     * the id <var>$projectId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO, CustomerDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function getByProjectId($projectId);

    /** Requests relationship entry creator by Customer id and Project id.
     *
     * This function creates a new entry in the table Requests
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the Customer.
     * @return int the number of rows that have been affected (it should be 1).
     * @see CustomerDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function create($customerId, $projectId);

    /** Requests relationship entry deleter by Customer id and Project id.
     *
     * This function deletes a entry in the table Requests
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the Customer we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see CustomerDAO, ProjectDAO
     * @throws {@link OperationErrorException}
     */
    public abstract function delete($customerId, $projectId);

}
