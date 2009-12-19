<?php

/** File for LoginAction
 *
 *  This file just contains {@link LoginAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

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
