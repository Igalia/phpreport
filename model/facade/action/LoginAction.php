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


/** File for LoginAction
 *
 *  This file just contains {@link LoginAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');

/** Login Action
 *
 *  This action is used for user's login.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class LoginAction extends Action{

    /** The User login
     *
     * This variable contains the login of the User who wants to login.
     *
     * @var string
     */
    private $userLogin;

    /** The User password
     *
     * This variable contains the password of the User who wants to login.
     *
     * @var string
     */
    private $userPassword;

    /** LoginAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $userLogin the login of the User who wants to login.
     * @param string $userPassword the password of the User who wants to login.
     */
    public function __construct($userLogin, $userPassword) {
        $this->userLogin=$userLogin;
        $this->userPassword=$userPassword;
        $this->preActionParameter="LOGIN_PREACTION";
        $this->postActionParameter="LOGIN_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that checks password and login and returns the User if there were no problems.
     *
     * @return UserVO the User that made login succesfully.
     * @throws {@link IncorrectLoginException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();
        return $dao->login($this->userLogin, $this->userPassword);

    }

}


/*//Test code

$action= new LoginAction('jjjameson', 'jaragunde');
var_dump($action);
$uservo = $action->execute();
var_dump($uservo);
*/
