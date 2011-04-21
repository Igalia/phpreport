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


/** File for DeassignUserFromProjectAction
 *
 *  This file just contains {@link DeassignUserFromProjectAction}.
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
 *  This action is used for deassigning a User from a Project by their ids.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeassignUserFromProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project which we want to dessign the User from.
     *
     * @var int
     */
    private $projectId;

    /** The User id
     *
     * This variable contains the id of the User we want to deassign.
     *
     * @var int
     */
    private $userId;

    /** DeassignUserFromProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User we want to deassign.
     * @param int $projectId the id of the Project which we want to deassign the User from.
     */
    public function __construct($userId, $projectId) {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->preActionParameter="DEASSIGN_USER_FROM_PROJECT_PREACTION";
        $this->postActionParameter="DEASSIGN_USER_FROM_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deassigns the User from the Project.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getProjectUserDAO();

        return $dao->delete($this->userId, $this->projectId);

    }

}


/*//Test code;

$action= new DeassignUserFromProjectAction(64, 1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
