<?php

/** File for DeassignUserToUserGroupAction
 *
 *  This file just contains {@link DeassignUserToUserGroupAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** UserGroup deassigning Action
 *
 *  This action is used for deassigning a User from a User Group by their ids.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeassignUserFromUserGroupAction extends Action{

    /** The UserGroup id
     *
     * This variable contains the id of the UserGroup which we want to deassign the User from.
     *
     * @var int
     */
    private $userGroupId;

    /** The User id
     *
     * This variable contains the id of the User we want to deassign.
     *
     * @var int
     */
    private $userId;

    /** DeassignUserToUserGroupAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User we want to deassign.
     * @param int $userGroupId the UserGroup which we want to deassign the User from.
     */
    public function __construct($userId, $userGroupId) {
        $this->userId = $userId;
        $this->userGroupId = $userGroupId;
        $this->preActionParameter="DEASSIGN_USER_FROM_USER_GROUP_PREACTION";
        $this->postActionParameter="DEASSIGN_USER_FROM_USER_GROUP_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deassigns the User from the User Group.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getBelongsDAO();

        return $dao->delete($this->userId, $this->userGroupId);

    }

}


/*//Test code;

$action= new DeassignUserToUserGroupAction(65, 1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
