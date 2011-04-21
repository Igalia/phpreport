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


/** File for PostgreSQLRequestsDAO
 *
 *  This file just contains {@link PostgreSQLRequestsDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/RequestsDAO/RequestsDAO.php');

/** DAO for relationship Requests in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link RequestsDAO}.
 *
 * @see RequestsDAO
 */
class PostgreSQLRequestsDAO extends RequestsDAO{

    /** Requests DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link RequestsDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see RequestsDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Value object constructor from edge A for PostgreSQL.
     *
     * This function creates a new {@link CustomerVO} with data retrieved from database edge A (Customer).
     *
     * @param array $row an array with the values from a row.
     * @return CustomerVO a {@link CustomerVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setAValues($row)
    {

    $customerVO = new CustomerVO();

    $customerVO->setId($row['id']);
    $customerVO->setName($row['name']);
    $customerVO->setType($row['type']);
    $customerVO->setUrl($row['url']);
    $customerVO->setSectorId($row['sectorid']);

    return $customerVO;
    }

    /** Value object constructor from edge B for PostgreSQL.
     *
     * This function creates a new {@link ProjectVO} with data retrieved from database edge B (Project).
     *
     * @param array $row an array with the values from a row.
     * @return ProjectVO a {@link ProjectVO} with its properties set to the values from <var>$row</var>.
     */
    protected function setBValues($row)
    {

    $projectVO = new ProjectVO();

    $projectVO->setId($row['id']);
    if (strtolower($row[activation]) == "t")
            $projectVO->setActivation(True);
    else
        $projectVO->setActivation(False);
    $projectVO->setInit(date_create($row['init']));
    $projectVO->setEnd(date_create($row['_end']));
    $projectVO->setInvoice($row['invoice']);
    $projectVO->setEstHours($row['est_hours']);
    $projectVO->setAreaId($row['areaid']);
    $projectVO->setType($row['type']);
    $projectVO->setDescription($row['description']);
    $projectVO->setMovedHours($row['moved_hours']);
    $projectVO->setSchedType($row['sched_type']);

    return $projectVO;
    }

    /** Requests entry retriever by id's for PostgreSQL.
     *
     * This function retrieves the row from Requests table with the id's <var>$customerId</var> and <var>$projectId</var>.
     *
     * @param int $customerId the id (that matches with a Customer) of the row we want to retrieve.
     * @param int $projectId the id (that matches with a Project) of the row we want to retrieve.
     * @return array an associative array with the data of the row.
     * @throws {@link SQLQueryErrorException}
     */
    protected function getByIds($customerId, $projectId) {
    if (!is_numeric($customerId))
        throw new SQLIncorrectTypeException($customerId);
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM requests WHERE customerid=" . $customerId . " AND projectid=" . $projectId;
    $result = $this->executeFromA($sql);
    return $result;
    }

    /** Projects retriever by Customer id for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are assigned through relationship Requests to the Customer with
     * the id <var>$customerId</var> and creates a {@link ProjectVO} with data from each row. We can retrieve only active projects or all them.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CustomerDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCustomerId($customerId, $active = False) {
    if (!is_numeric($customerId))
        throw new SQLIncorrectTypeException($customerId);
        $sql = "SELECT project.* FROM requests LEFT JOIN project ON requests.projectid=project.id WHERE requests.customerid=" . $customerId;
    if ($active)
        $sql = $sql . " AND project.activation = 'True'";
    $sql = $sql . " ORDER BY project.id ASC";
    $result = $this->executeFromA($sql);
    return $result;
    }

    /** Customers retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Customer table that are assigned through relationship Requests to the Project with
     * the id <var>$projectId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO, CustomerDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getByProjectId($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT customer.* FROM requests LEFT JOIN customer ON requests.customerid=customer.id WHERE requests.projectid=" . $projectId . " ORDER BY customer.id ASC";
    $result = $this->executeFromB($sql);
    return $result;
    }

    /** Requests relationship entry creator by Customer id and Project id for PostgreSQL.
     *
     * This function creates a new entry in the table Requests
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer we want to relate to the Project.
     * @param int $projectId the id of the Project we want to relate to the Customer.
     * @return int the number of rows that have been affected (it should be 1).
     * @see CustomerDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function create($customerId, $projectId) {
    $affectedRows = 0;

        // Check for a requests entry ID.
        $currBelongs = $this->getByIds($customerId, $projectId);

        // If it doesn't exist, then create.
        if(sizeof($currBelongs) == 0) {
        $sql = "INSERT INTO requests (customerid, projectid) VALUES (" . $customerId . ", " . $projectId . ")";

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $affectedRows = pg_affected_rows($res);
    }

    return $affectedRows;

    }

    /** Requests relationship entry deleter by Customer id and Project id for PostgreSQL.
     *
     * This function deletes a entry in the table Requests
     * with the Customer id <var>$customerId</var> and the Project id <var>$projectId</var>.
     *
     * @param int $customerId the id of the Customer whose relation to the Project we want to delete.
     * @param int $projectId the id of the Project whose relation to the Customer we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see CustomerDAO, ProjectDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function delete($customerId, $projectId) {
        $affectedRows = 0;

        // Check for a requests entry ID.
        $currBelongs = $this->getByIds($customerId, $projectId);

        // If it exists, then delete.
        if(sizeof($currBelongs) > 0) {
            $sql = "DELETE FROM requests WHERE customerid=" . $customerId . " AND projectid=" . $projectId;

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLRequestsDAO();

// We create a new entry

$customerId = 1;

$projectId = 1;

$dao->create($customerId, $projectId);

// We search for the new entry from side A

$projects = $dao->getByCustomerId($customerId);

foreach ($projects as $project)
    print ("Project for customer ". $customerId ." : " . $project->getDescription() . "\n");

// We search for the new entry from side B

$customers = $dao->getByProjectId($projectId);

foreach ($customers as $customer)
    print ("Customer for project ". $projectId ." : " . $customer->getName() . "\n");

// We delete the new entry

$dao->delete($customerId, $projectId);

$dao = new PostgreSQLRequestsDAO();

$projects = $dao->getByCustomerId(14);

var_dump($projects);*/
