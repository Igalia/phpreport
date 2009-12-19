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


/** File for GetUserTasksByLoginDateAction
 *
 *  This file just contains {@link GetUserTasksByLoginDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Get Tasks by User Id and Date Action
 *
 *  This action is used for retrieving tasks of a User on a date by his/her login.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserTasksByLoginDateAction extends Action{

    /** The User
     *
     * This variable contains the User whose tasks we want to retrieve.
     *
     * @var UserVO
     */
    private $userVO;

    /** The date
     *
     * This variable contains the date whose tasks we want to retrieve.
     *
     * @var DateTime
     */
    private $date;

    /** CreateUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User whose tasks we want to retrieve.
     * @param DateTime $date the date whose tasks we want to retrieve.
     */
    public function __construct(UserVO $userVO, DateTime $date) {
        $this->userVO = $userVO;
    $this->date = $date;
        $this->preActionParameter="GET_USER_TASKS_BY_LOGIN_DATE_PREACTION";
        $this->postActionParameter="GET_USER_TASKS_BY_LOGIN_DATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Tasks.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao1 = DAOFactory::getTaskDAO();
    $dao2 = DAOFactory::getUserDAO();

    $user = $dao2->getByUserLogin($this->userVO->getLogin());

    if (is_null($user))
        return NULL;

        return $dao1->getByUserIdDate($user->getId(), $this->date);

    }

}


/*//Test code

$uservo= new UserVO();
$uservo->setLogin('jjsantos');
$uservo->setPassword('jaragunde');
$action= new CreateUserAction($uservo);
var_dump($action);
$action->execute();
var_dump($uservo);
*/
