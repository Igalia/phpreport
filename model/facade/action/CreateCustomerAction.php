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


/** File for CreateCustomerAction
 *
 *  This file just contains {@link CreateCustomerAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');

/** Create Customer Action
 *
 *  This action is used for creating a new Customer.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCustomerAction extends Action{

    /** The Customer
     *
     * This variable contains the Customer we want to create.
     *
     * @var CustomerVO
     */
    private $customer;

    /** CreateCustomerAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomerVO $customer the Customer value object we want to create.
     */
    public function __construct(CustomerVO $customer) {
        $this->customer=$customer;
        $this->preActionParameter="CREATE_CUSTOMER_PREACTION";
        $this->postActionParameter="CREATE_CUSTOMER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Customer, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    protected function doExecute() {

        $dao = DAOFactory::getCustomerDAO();
        if ($dao->create($this->customer)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$customervo = new CustomerVO();

$customervo->setName("Telenet");
$customervo->setType("Internet");
$customervo->setSectorId(1);

$action= new CreateCustomerAction($customervo);
var_dump($action);
$action->execute();
var_dump($customervo);
*/
