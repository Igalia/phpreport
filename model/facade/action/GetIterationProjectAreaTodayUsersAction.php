<?php

/** File for GetIterationProjectAreaTodayUsersAction
 *
 *  This file just contains {@link GetIterationProjectAreaTodayUsersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Iteration Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Iteration Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetIterationProjectAreaTodayUsersAction extends Action{

    /** The Iteration Id
     *
     * @param int $iterationId the id of the Project Iteration whose related Users (through Area) we want to retrieve.
     *
     * @var int
     */
    private $iterationId;

    /** GetProjectAreaTodayUsersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $iterationId the id of the Project Iteration whose related Users (through Area) we want to retrieve.
     */
    public function __construct($iterationId) {
        $this->iterationId=$iterationId;
        $this->preActionParameter="GET_ITERATION_PROJECT_AREA_TODAY_USERS_PREACTION";
        $this->postActionParameter="GET_ITERATION_PROJECT_AREA_TODAY_USERS_POSTACTION";

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

        return $dao->getByIterationProjectAreaToday($this->iterationId);

    }

}


/*//Test code;

$action= new GetIterationProjectAreaTodayUsersAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);*/
