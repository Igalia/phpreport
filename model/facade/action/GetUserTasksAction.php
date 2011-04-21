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


/** File for GetUserTasksAction
 *
 *  This file just contains {@link GetUserTasksAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');


/** Get User Tasks Action
 *
 *  This action is used for retrieving all Tasks related to a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserTasksAction extends Action{

    /** The User
     *
     * This variable contains the User whose Tasks we want to retieve.
     *
     * @var UserVO
     */
    private $userVO;

    /** GetUserTasksAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $userVO the User whose Tasks we want to retieve.
     */
    public function __construct(UserVO $userVO) {
        $this->userVO=$userVO;
        $this->preActionParameter="GET_USER_REPORTS_PREACTION";
        $this->postActionParameter="GET_USER_REPORTS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Tasks from persistent storing.
     *
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskDAO();

        return $dao->getByUserId($this->userVO->getId());

    }

}


/*//Test code;

$dao = DAOFactory::getUserDAO();

$user = $dao->getById(1);

$action= new GetUserTasksAction($user);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
