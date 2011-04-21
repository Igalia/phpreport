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


/** File for GetCustomersByProjectUserAction
 *
 *  This file just contains {@link GetCustomersByProjectUserAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Get Customers from a User's Projects Action
 *
 *  This action is used for retrieving information about Customers of Projects done by a User. If no User is specified, it returns all Customers.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomersByProjectUserAction extends Action{

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only data related to active Projects.
     *
     * @var bool
     */
    private $active;

    /** The User.
     *
     * This variable contains the User whose Projects' Customers we want to retrieve.
     *
     * @var UserVO
     */
    private $userVO;

    /** Order field
     *
     * This variable contains the optional parameter for ordering value objects by a specific field.
     *
     * @var string
     */
    private $order;

    /** GetCustomersByProjectUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $userVO the User whose Projects' Customers we want to retrieve.
     * @param bool $active optional parameter for obtaining only data related to active Projects (by default it returns all them).
     * @param string $order optional parameter for sorting value objects in a specific way (by default, by their internal id).
     */
    public function __construct(UserVO $userVO = NULL, $active = False, $order = 'id') {
        $this->userVO = $userVO;
        $this->active = $active;
        $this->order = $order;
        $this->preActionParameter="GET_CUSTOMERS_BY_PROJECT_USER_PREACTION";
        $this->postActionParameter="GET_CUSTOMERS_BY_PROJECT_USER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Customers.
     *
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomerDAO();

    if (is_null($this->userVO))
      return $dao->getAll($this->active, $this->order);
    else
    {
      return $dao->getByProjectUserLogin($this->userVO->getLogin(), $this->active, $this->order);
    }

    }

}


/*//Test code;

$user = new UserVO();

$user->setLogin("jaragunde");

$action= new GetCustomersByProjectUserAction($user);
//var_dump($action);
$result = $action->execute();
var_dump($result);*/
