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


/** File for GetUserByLoginAction
 *
 *  This file just contains {@link GetUserByLoginAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get User by Login Action
 *
 *  This action is used for retrieving a User by its login.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserByLoginAction extends Action{

    /** The User login
     *
     * This variable contains the login of the User we want to retieve.
     *
     * @var string
     */
    private $login;

    /** GetUserByLoginAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $login the login of the User we want to retieve.
     */
    public function __construct($login) {
        $this->login=$login;
        $this->preActionParameter="GET_USER_BY_LOGIN_PREACTION";
        $this->postActionParameter="GET_USER_BY_LOGIN_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the User from persistent storing.
     *
     * @return UserVO the User as a {@link UserVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getByUserLogin($this->login);

    }

}


/*//Test code;

$action= new GetUserByLoginAction("jaragunde");
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
