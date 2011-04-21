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


/** File for CreateUserAction
 *
 *  This file just contains {@link CreateUserAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Create User Action
 *
 *  This action is used for creating a new User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateUserAction extends Action{

    /** The User
     *
     * This variable contains the User we want to create.
     *
     * @var UserVO
     */
    private $user;

    /** CreateUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $user the User value object we want to create.
     */
    public function __construct(UserVO $user) {
        $this->user=$user;
        $this->preActionParameter="CREATE_USER_PREACTION";
        $this->postActionParameter="CREATE_USER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new User, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();
        if ($dao->create($this->user)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$uservo= new UserVO();
$uservo->setLogin('jjjameson');
$uservo->setPassword('jaragunde');
$action= new CreateUserAction($uservo);
var_dump($action);
$action->execute();
var_dump($uservo);*/
