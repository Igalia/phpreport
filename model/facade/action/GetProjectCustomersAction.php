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


/** File for GetProjectCustomersAction
 *
 *  This file just contains {@link GetProjectCustomersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');


/** Get Project Customers Action
 *
 *  This action is used for retrieving all Customers related to a Project through relationship Requests.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectCustomersAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Customers we want to retieve.
     *
     * @var int
     */
    private $projectId;

    /** GetProjectCustomersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $projectId the id of the Project whose Customers we want to retieve.
     */
    public function __construct($projectId) {
        $this->projectId=$projectId;
        $this->preActionParameter="GET_PROJECT_CUSTOMERS_PREACTION";
        $this->postActionParameter="GET_PROJECT_CUSTOMERS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Customers from persistent storing.
     *
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

        return $dao->getCustomers($this->projectId);

    }

}


/*//Test code;

$action= new GetProjectCustomersAction(4);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
