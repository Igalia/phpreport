<?php

/** File for DeleteUserGroupAction
 *
 *  This file just contains {@link DeleteUserGroupAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserGroupVO.php');

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
