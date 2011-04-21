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


/** File for AssignUserToUserGroupAction
 *
 *  This file just contains {@link AssignUserToUserGroupAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** UserGroup assigning Action
 *
 *  This action is used for assigning a User to a User Group by their ids.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class AssignUserToUserGroupAction extends Action{

    /** The UserGroup id
     *
     * This variable contains the id of the UserGroup which we want to assign the User to.
     *
     * @var int
     */
    private $userGroupId;

    /** The User id
     *
     * This variable contains the id of the User we want to assign.
     *
     * @var int
     */
    private $userId;

    /** AssignUserToUserGroupAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User we want to assign.
     * @param int $userGroupId the UserGroup which we want to assign the User to.
     */
    public function __construct($userId, $userGroupId) {
        $this->userId = $userId;
        $this->userGroupId = $userGroupId;
        $this->preActionParameter="ASSIGN_USER_TO_USER_GROUP_PREACTION";
        $this->postActionParameter="ASSIGN_USER_TO_USER_GROUP_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that assigns the User to the User Group.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getBelongsDAO();

        return $dao->create($this->userId, $this->userGroupId);

    }

}


/*//Test code;

$action= new AssignUserToUserGroupAction(64, 1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
