<?php

/** File for GetModuleProjectAreaTodayUsersAction
 *
 *  This file just contains {@link GetModuleProjectAreaTodayUsersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Module Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Module Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetModuleProjectAreaTodayUsersAction extends Action{

    /** The Module Id
     *
     * @param int $moduleId the id of the Project Module whose related Users (through Area) we want to retrieve.
     *
     * @var int
     */
    private $moduleId;

    /** GetProjectAreaTodayUsersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $moduleId the id of the Project Module whose related Users (through Area) we want to retrieve.
     */
    public function __construct($moduleId) {
        $this->moduleId=$moduleId;
        $this->preActionParameter="GET_MODULE_PROJECT_AREA_TODAY_USERS_PREACTION";
        $this->postActionParameter="GET_MODULE_PROJECT_AREA_TODAY_USERS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Users from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getByModuleProjectAreaToday($this->moduleId);

    }

}


/*//Test code;

$action= new GetModuleProjectAreaTodayUsersAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);*/
