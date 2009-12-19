<?php

/** File for DeleteCustomerAction
 *
 *  This file just contains {@link DeleteCustomerAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomerVO.php');

/** Delete Customer Action
 *
 *  This action is used for deleting a Customer.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteCustomerAction extends Action{

    /** The Customer
     *
     * This variable contains the Customer we want to delete.
     *
     * @var CustomerVO
     */
    private $customer;

    /** DeleteCustomerAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomerVO $customer the Customer value object we want to delete.
     */
    public function __construct(CustomerVO $customer) {
        $this->customer=$customer;
        $this->preActionParameter="DELETE_CUSTOMER_PREACTION";
        $this->postActionParameter="DELETE_CUSTOMER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Customer from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getCustomerDAO();
        if ($dao->delete($this->customer)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$customervo = new CustomerVO();

$customervo->setId(43);
$customervo->setName("Telenet");

$action= new DeleteCustomerAction($customervo);
var_dump($action);
$action->execute();
var_dump($customervo);
*/
