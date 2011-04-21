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


/** File for GetAllUserGroupsAction
 *
 *  This file just contains {@link GetAllUserGroupsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get all UserGroups Action
 *
 *  This action is used for retrieving all User Groups.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllUserGroupsAction extends Action{

    /** GetAllUserGroupsAction constructor.
     *
     * This is just the constructor of this action.
     */
    public function __construct() {
        $this->preActionParameter="GET_ALL_USER_GROUPS_PREACTION";
        $this->postActionParameter="GET_ALL_USER_GROUPS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the User Groups from persistent storing.
     *
     * @return array an array with value objects {@link UserGroupVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserGroupDAO();

        return $dao->getAll();

    }

}


/*//Test code;

$action= new GetAllUserGroupsAction();
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
