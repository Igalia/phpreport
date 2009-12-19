<?php

/** File for UpdateCustomerAction
 *
 *  This file just contains {@link UpdateCustomerAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomerVO.php');

/** Update Customer Action
 *
 *  This action is used for updating a Customer.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateCustomerAction extends Action{

    /** The Customer
     *
     * This variable contains the Customer we want to update.
     *
     * @var CustomerVO
     */
    private $customer;

    /** UpdateCustomerAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomerVO $customer the Customer value object we want to update.
     */
    public function __construct(CustomerVO $customer) {
        $this->customer=$customer;
        $this->preActionParameter="UPDATE_CUSTOMER_PREACTION";
        $this->postActionParameter="UPDATE_CUSTOMER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Customer on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getCustomerDAO();
        if ($dao->update($this->customer)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$customervo = new CustomerVO();

$customervo->setId(43);
$customervo->setName("Telenetos");
$customervo->setType("Internet");
$customervo->setSectorId(1);

$action= new UpdateCustomerAction($customervo);
var_dump($action);
$action->execute();
var_dump($customervo);
*/
