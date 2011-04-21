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


/** File for GetAllCustomersAction
 *
 *  This file just contains {@link GetAllCustomersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get all Customers Action
 *
 *  This action is used for retrieving all Customers.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllCustomersAction extends Action{

    /** Order field
     *
     * This variable contains the optional parameter for ordering value objects by a specific field.
     *
     * @var string
     */
    private $order;

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only data related to active Projects.
     *
     * @var bool
     */
    private $active;

    /** GetAllCustomersAction constructor.
     *
     * This is just the constructor of this action.
     * @param string $order optional parameter for sorting value objects in a specific way (by default, by their internal id).
     */
    public function __construct($active = False, $order = 'id') {
        $this->preActionParameter="GET_ALL_CUSTOMERS_PREACTION";
        $this->postActionParameter="GET_ALL_CUSTOMERS_POSTACTION";
        $this->active = $active;
        $this->order = $order;
    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Customers from persistent storing.
     *
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {
        $dao = DAOFactory::getCustomerDAO();
        return $dao->getAll($this->active, $this->order);
    }

}


//Test code;

/*$action= new GetAllCustomersAction();
var_dump($action);
$result = $action->execute();
var_dump($result);
 */
