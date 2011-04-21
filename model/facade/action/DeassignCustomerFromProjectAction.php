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


/** File for DeassignCustomerFromProjectAction
 *
 *  This file just contains {@link DeassignCustomerFromProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Project deassigning Action
 *
 *  This action is used for deassigning a Customer from a Project by their ids.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeassignCustomerFromProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project which we want to dessign the Customer from.
     *
     * @var int
     */
    private $projectId;

    /** The Customer id
     *
     * This variable contains the id of the Customer we want to deassign.
     *
     * @var int
     */
    private $customerId;

    /** DeassignCustomerFromProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $customerId the id of the Customer we want to deassign.
     * @param int $projectId the id of the Project which we want to deassign the Customer from.
     */
    public function __construct($customerId, $projectId) {
        $this->customerId = $customerId;
        $this->projectId = $projectId;
        $this->preActionParameter="DEASSIGN_CUSTOMER_FROM_PROJECT_PREACTION";
        $this->postActionParameter="DEASSIGN_CUSTOMER_FROM_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deassigns the Customer from the Project.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getRequestsDAO();

        return $dao->delete($this->customerId, $this->projectId);

    }

}


/*//Test code;

$action= new DeassignCustomerFromProjectAction(64, 1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
