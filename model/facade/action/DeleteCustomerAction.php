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


/** File for DeleteCustomerAction
 *
 *  This file just contains {@link DeleteCustomerAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');

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
