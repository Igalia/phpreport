<?php

/** File for GetUserProjectsAction
 *
 *  This file just contains {@link GetUserProjectsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');


/** Get User Projects Action
 *
 *  This action is used for retrieving all Projects related to a User through relationship UserProject.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUserProjectsAction extends Action{

    /** The User Id
     *
     * This variable contains the id of the User whose Projects we want to retieve.
     *
     * @var int
     */
    private $userId;

    /** GetUserProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId the id of the User whose Projects we want to retieve.
     */
    public function __construct($userId) {
        $this->userId=$userId;
        $this->preActionParameter="GET_USER_PROJECTS_PREACTION";
        $this->postActionParameter="GET_USER_PROJECTS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Projects from persistent storing.
     *
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getProjectsUser($this->userId);

    }

}


/*//Test code;

$action= new GetUserProjectsAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
