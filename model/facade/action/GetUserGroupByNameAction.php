<?php

/** File for GetUserGroupByNameAction
 *
 *  This file just contains {@link GetUserGroupByNameAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get UserGroup by Name Action
 *
 *  This action is used for retrieving a UserGroup by its name.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserGroupByNameAction extends Action{

    /** The UserGroup name
     *
     * This variable contains the name of the UserGroup we want to retieve.
     *
     * @var string
     */
    private $name;

    /** GetUserGroupByNameAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param string $name the name of the UserGroup we want to retieve.
     */
    public function __construct($name) {
        $this->name=$name;
        $this->preActionParameter="GET_USER_GROUP_BY_NAME_PREACTION";
        $this->postActionParameter="GET_USER_GROUP_BY_NAME_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the User Group from persistent storing.
     *
     * @return UserGroupVO the UserGroup as a {@link UserGroupVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserGroupDAO();

        return $dao->getByUserGroupName($this->name);

    }

}


/*//Test code;

$action= new GetUserGroupByNameAction("staff");
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
