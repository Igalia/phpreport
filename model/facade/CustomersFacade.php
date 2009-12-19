<?php

/** File for CustomersFacade
 *
 *  This file just contains {@link CustomersFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/CreateCustomerAction.php');
include_once('phpreport/model/facade/action/GetCustomerAction.php');
include_once('phpreport/model/facade/action/DeleteCustomerAction.php');
include_once('phpreport/model/facade/action/UpdateCustomerAction.php');
include_once('phpreport/model/facade/action/CreateSectorAction.php');
include_once('phpreport/model/facade/action/DeleteSectorAction.php');
include_once('phpreport/model/facade/action/UpdateSectorAction.php');
include_once('phpreport/model/facade/action/GetCustomersByProjectUserAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomerVO.php');
include_once('phpreport/model/vo/SectorVO.php');

/** Customers Facade
 *
 *  This Facade contains the functions used in tasks related to Customers.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class CustomersFacade {

    /** Get Customer Function
     *
     *  This action is used for retrieving a Customer.
     *
     * @param int $id the database identifier of the Customer we want to retieve.
     * @return CustomerVO the Customer as a {@link CustomerVO} with its properties set to the values from the row.
     */
    static function GetCustomer($customerId) {

    $action = new GetCustomerAction($customerId);

    return $action->execute();

    }

    /** Create Customer Function
     *
     *  This function is used for creating a new Customer.
     *
     * @param CustomerVO $customer the Customer value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function CreateCustomer(CustomerVO $customer) {

    $action = new CreateCustomerAction($customer);

    return $action->execute();

    }

    /** Delete Customer Function
     *
     *  This function is used for deleting a Customer.
     *
     * @param CustomerVO $customer the Customer value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteCustomer(CustomerVO $customer) {

    $action = new DeleteCustomerAction($customer);

    return $action->execute();

    }

    /** Update Customer Function
     *
     *  This function is used for updating a Customer.
     *
     * @param CustomerVO $customer the Customer value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function UpdateCustomer(CustomerVO $customer) {

    $action = new UpdateCustomerAction($customer);

    return $action->execute();

    }

    /** Create Customer Function
     *
     *  This function is used for creating a new Customer.
     *
     * @param CustomerVO $customer the Customer value object we want to create.
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    static function GetCustomersByProjectUser(UserVO $user=NULL, $active = False) {

    $action = new GetCustomersByProjectUserAction($user, $active);

    return $action->execute();

    }

    /** Create Sector Function
     *
     *  This function is used for creating a new Sector.
     *
     * @param SectorVO $sector the Sector value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateSector(SectorVO $sector) {

    $action = new CreateSectorAction($sector);

    return $action->execute();

    }

    /** Delete Sector Function
     *
     *  This function is used for deleting a Sector.
     *
     * @param SectorVO $sector the Sector value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteSector(SectorVO $sector) {

    $action = new DeleteSectorAction($sector);

    return $action->execute();

    }

    /** Update Sector Function
     *
     *  This function is used for updating a Sector.
     *
     * @param SectorVO $sector the Sector value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateSector(SectorVO $sector) {

    $action = new UpdateSectorAction($sector);

    return $action->execute();

    }

}



/*include_once('phpreport/model/vo/UserVO.php');

$user = new UserVO();

$user->setLogin("jaragunde");

$result = CustomersFacade::GetCustomersByProjectUser($user);

var_dump($result);*/
