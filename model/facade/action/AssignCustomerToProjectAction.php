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


/** File for AssignCustomerToProjectAction
 *
 *  This file just contains {@link AssignCustomerToProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Project assigning Action
 *
 *  This action is used for assigning a Customer to a Project by their ids.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class AssignCustomerToProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project which we want to assign the Customer to.
     *
     * @var int
     */
    private $projectId;

    /** The Customer id
     *
     * This variable contains the id of the Customer we want to assign.
     *
     * @var int
     */
    private $customerId;

    /** AssignCustomerToProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $customerId the id of the Customer we want to assign.
     * @param int $projectId the id of the Project which we want to assign the Customer to.
     */
    public function __construct($customerId, $projectId) {
        $this->customerId = $customerId;
        $this->projectId = $projectId;
        $this->preActionParameter="ASSIGN_CUSTOMER_TO_PROJECT_PREACTION";
        $this->postActionParameter="ASSIGN_CUSTOMER_TO_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that assigns the Customer to the Project.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getRequestsDAO();

        return $dao->create($this->customerId, $this->projectId);

    }

}


/*//Test code;

$action= new AssignCustomerToProjectAction(64, 1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
