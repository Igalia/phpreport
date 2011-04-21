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


/** File for DeleteUserGroupAction
 *
 *  This file just contains {@link DeleteUserGroupAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGroupVO.php');

/** Delete User Group Action
 *
 *  This action is used for deleting a User Group.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteUserGroupAction extends Action{

    /** The User Group
     *
     * This variable contains the User Group we want to delete.
     *
     * @var UserGroupVO
     */
    private $userGroup;

    /** DeleteUserGroupAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserGroupVO $userGroup the User Group value object we want to delete.
     */
    public function __construct(UserGroupVO $userGroup) {
        $this->userGroup=$userGroup;
        $this->preActionParameter="DELETE_USER_GROUP_PREACTION";
        $this->postActionParameter="DELETE_USER_GROUP_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the User Group from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserGroupDAO();
        if ($dao->delete($this->userGroup)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$usergroupvo= new UserGroupVO();
$usergroupvo->setId(1);
$action= new DeleteUserGroupAction($usergroupvo);
var_dump($action);
$action->execute();
var_dump($usergroupvo);
*/
