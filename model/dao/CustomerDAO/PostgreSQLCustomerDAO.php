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


/** File for PostgreSQLCustomerDAO
 *
 *  This file just contains {@link PostgreSQLCustomerDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/CustomerDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/PostgreSQLTaskDAO.php');

/** DAO for Customers in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link CustomerDAO}.
 *
 * @see CustomerDAO, CustomerVO
 */
class PostgreSQLCustomerDAO extends CustomerDAO{

    /** Customer DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link CustomerDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see CustomerDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** Customer value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CustomerVO} with data retrieved from database.
     *
     * @param array $row an array with the Customer values from a row.
     * @return CustomerVO a {@link CustomerVO} with its properties set to the values from <var>$row</var>.
     * @see CustomerVO
     */
    protected function setValues($row)
    {

    $customerVO = new CustomerVO();

    $customerVO->setId($row['id']);
    $customerVO->setName($row['name']);
    $customerVO->setType($row['type']);
    $customerVO->setUrl($row['url']);
    $customerVO->setSectorId($row['sectorid']);

    return $customerVO;
    }

    /** Customer retriever by id.
     *
     * This function retrieves the row from Customer table with the id
     * <var>$customerId</var> and creates a {@link CustomerVO} with its data.
     *
     * @param int $customerId the id of the row we want to retrieve.
     * @return CustomerVO a value object {@link CustomerVO} with its properties
     * set to the values from the row, or NULL if no customer was found for that id.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($customerId) {
        if (!is_numeric($customerId))
            throw new SQLIncorrectTypeException($customerId);
        $result = $this->runSelectQuery(
            "SELECT * FROM customer WHERE id=:customerid",
            [':customerid' => $customerId],
            'CustomerVO');
        return $result[0] ?? NULL;
    }

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
     * @throws {@link SQLQueryErrorException}
     */
    public function getBySectorId($sectorId, $orderField = 'id') {
    $sql = "SELECT * FROM customer WHERE sectorid=" . $sectorId . " ORDER BY " . $orderField  . " ASC";
    $result = $this->execute($sql);
    return $result;
    }

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
    public function getByProjectUserLogin($userLogin, $active = False, $orderField = 'id') {
        $sql = "SELECT * FROM customer WHERE id IN (SELECT customerid FROM requests WHERE projectid IN (SELECT id FROM project WHERE";
        if ($active)
            $sql = $sql . " activation = 'True' AND";
        $sql = $sql . " id IN (SELECT projectid FROM project_usr WHERE usrid = ( SELECT id FROM usr WHERE login = " . DBPostgres::checkStringNull($userLogin) . " )))) ORDER BY " . $orderField  . " ASC";
        $result = $this->execute($sql);
        return $result;
    }

    /** Customer retriever.
     *
     * This function retrieves all rows from Customer table and creates a {@link CustomerVO} with data from each row.
     *
     * @param bool $active optional parameter for obtaining only data related to active Projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll($active = False, $orderField = 'id') {
        if ($active)
            $sql = "SELECT * FROM customer WHERE id IN " .
                      "(SELECT customerid FROM project WHERE activation = 'True') " .
                   "ORDER BY :orderfield ASC";
        else
            $sql = "SELECT * FROM customer ORDER BY :orderfield ASC";
        return $this->runSelectQuery($sql, [':orderfield' => $orderField], 'CustomerVO');
    }

    /** Customer updater.
     *
     * This function updates the data of a Customer by its {@link CustomerVO}.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(CustomerVO $customerVO) {
        $affectedRows = 0;

        if($customerVO->getId() >= 0) {
            $currCustomerVO = $this->getById($customerVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currCustomerVO) > 0) {

        $sql = "UPDATE customer SET name=" . DBPostgres::checkStringNull($customerVO->getName()) . ", type="  . DBPostgres::checkStringNull($customerVO->getType()) . ", url="  . DBPostgres::checkStringNull($customerVO->getUrl()) . ", sectorid="  . DBPostgres::checkNull($customerVO->getSectorId()). " WHERE id=".$customerVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Customer creator.
     *
     * This function creates a new row for a Customer by its {@link CustomerVO}. The internal id of <var>$customerVO</var> will be set after its creation.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(CustomerVO $customerVO) {
        $affectedRows = 0;

    $sql = "INSERT INTO customer (name, type, url, sectorid) VALUES (" . DBPostgres::checkStringNull($customerVO->getName()) . ", "  . DBPostgres::checkStringNull($customerVO->getType()) . ", "  . DBPostgres::checkStringNull($customerVO->getUrl()) . ", "  . DBPostgres::checkNull($customerVO->getSectorId()) . ")";

        $res = pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $customerVO->setId(DBPostgres::getId($this->connect, "customer_id_seq"));

    $affectedRows = pg_affected_rows($res);

    return $affectedRows;

    }

    /** Customer deleter.
     *
     * This function deletes the data of a Customer by its {@link CustomerVO}.
     *
     * @param CustomerVO $customerVO the {@link CustomerVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(CustomerVO $customerVO) {
        $affectedRows = 0;

        // Check for a user ID.
        if($customerVO->getId() >= 0) {
            $currCustomerVO = $this->getById($customerVO->getId());
        }

        // Otherwise delete a user.
        if(sizeof($currCustomerVO) > 0) {
            $sql = "DELETE FROM customer WHERE id=".$customerVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




//Uncomment these lines inuserGroup order to do a simple test of the Dao



$dao = new PostgreSQLCustomerDAO();

/*$userId = 114;

var_dump($dao->getByProjectUser($userId));

// We create a new customer

$customer = new CustomerVO();

$customer->setName("Telenet");
$customer->setType("Internet");
$customer->setSectorId(1);

$dao->create($customer);

print ("New customer Id is ". $customer->getId() ."\n");

// We search for the new Id

$customer = $dao->getById($customer->getId());

print ("New customer Id found is ". $customer->getId() ."\n");

// We update the customer with a differente name

$customer->setName("Intranet");

$dao->update($customer);

// We search for the new name

$customer = $dao->getById($customer->getId());

print ("New customer name found is ". $customer->getName() ."\n");

// We delete the new user

$dao->delete($customer);

$customs = $dao->getAll(True);

var_dump($customs);*/
