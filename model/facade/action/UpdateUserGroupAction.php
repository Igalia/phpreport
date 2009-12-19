<?php

/** File for UpdateUserGroupAction
 *
 *  This file just contains {@link UpdateUserGroupAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/UserGroupVO.php');

/** Update User Group Action
 *
 *  This action is used for updating a User Group.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateUserGroupAction extends Action{

    /** The User Group
     *
     * This variable contains the User Group we want to update.
     *
     * @var UserVO
     */
    private $extraHour;

    /** UpdateUserGroupAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param UserGroupVO $userGroup the User Group value object we want to update.
     */
    public function __construct(UserGroupVO $userGroup) {
        $this->userGroup=$userGroup;
        $this->preActionParameter="UPDATE_USER_GROUP_PREACTION";
        $this->postActionParameter="UPDATE_USER_GROUP_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the User Group on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserGroupDAO();
        if ($dao->update($this->userGroup)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$usergroupvo= new UserGroupVO();
$usergroupvo->setName('Mutants');
$usergroupvo->setId(1);
$action= new UpdateUserGroupAction($usergroupvo);
var_dump($action);
$action->execute();
var_dump($usergroupvo);
*/
