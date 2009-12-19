<?php

/** File for DeleteUserAction
 *
 *  This file just contains {@link DeleteUserAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserVO.php');

/** Delete User Action
 *
 *  This action is used for deleting a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteUserAction extends Action{

    /** The User
     *
     * This variable contains the User we want to delete.
     *
     * @var UserVO
     */
    private $user;

    /** DeleteUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserVO $user the User value object we want to delete.
     */
    public function __construct(UserVO $user) {
        $this->user=$user;
        $this->preActionParameter="DELETE_USER_PREACTION";
        $this->postActionParameter="DELETE_USER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the User from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserDAO();
        if ($dao->delete($this->user)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$uservo= new UserVO();
$uservo->setId(82);
$action= new DeleteUserAction($uservo);
var_dump($action);
$action->execute();
var_dump($uservo);
*/
