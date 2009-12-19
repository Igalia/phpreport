<?php

/** File for GetProjectsByCustomerUserLoginAction
 *
 *  This file just contains {@link GetProjectsByCustomerUserLoginAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Get Projects by Customer and User Login Action
 *
 *  This action is used for retrieving information about Projects related to a Customer and optionally an User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectsByCustomerUserLoginAction extends Action{

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only active projects.
     *
     * @var bool
     */
    private $active;

    /** The Customer id.
     *
     * This variable contains the id of the Customer whose Projects we want to retrieve.
     *
     * @var int
     */
    private $customerId;

    /** The User login.
     *
     * This variable contains the login of the User whose Projects we want to retrieve.
     *
     * @var string
     */
    private $userLogin;

    /** GetProjectsByCustomerUserLoginAction constructor.
     *
     * This is just the constructor of this action. We pass the id of the Customer, and an optional parameter
     * if we want only active projects.
     *
     * @param int $customerId the id of the Customer whose Projects we want to retrieve.
     * @param string $userLogin optional parameter for obtaining only the Projects related to an User.
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     */
    public function __construct($customerId, $userLogin = NULL, $active = False) {
        $this->customerId = $customerId;
    $this->active = $active;
    $this->userLogin = $userLogin;
        $this->preActionParameter="GET_PROJECTS_BY_CUSTOMER_USER_LOGIN_PREACTION";
        $this->postActionParameter="GET_PROJECTS_BY_CUSTOMER_USER_LOGIN_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns the Projects.
     *
     * @return array an array with the rows of Projects related to that Customer.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

    return $dao->getByCustomerUserLogin($this->customerId, $this->userLogin, $this->active);

    }

}


/*//Test code;

$action= new GetProjectsByCustomerUserLoginAction(10, 'jaragunde');
//var_dump($action);
$result = $action->execute();
var_dump($result);
*/
