<?php

/** File for GetUserAction
 *
 *  This file just contains {@link GetUserAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get User Action
 *
 *  This action is used for retrieving a User.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserAction extends Action{

    /** The User id
     *
     * This variable contains the id of the User we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetUserAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the User we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_USER_PREACTION";
        $this->postActionParameter="GET_USER_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the User from persistent storing.
     *
     * @return UserVO the User as a {@link UserVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetUserAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
