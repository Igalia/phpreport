<?php

/** File for GetProjectIterationsAction
 *
 *  This file just contains {@link GetProjectIterationsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');


/** Get Project Iterations Action
 *
 *  This action is used for retrieving all Iterations related to a Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectIterationsAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Iterations we want to retieve.
     *
     * @var int
     */
    private $projectId;

    /** GetProjectIterationsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $projectId the id of Project whose Iterations we want to retieve.
     */
    public function __construct($projectId) {
        $this->projectId=$projectId;
        $this->preActionParameter="GET_PROJECT_ITERATIONS_PREACTION";
        $this->postActionParameter="GET_PROJECT_ITERATIONS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Iterations from persistent storing.
     *
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getIterationDAO();

        return $dao->getByProjectId($this->projectId);

    }

}


/*//Test code;

$dao = DAOFactory::getProjectDAO();

$project = $dao->getById(1);

$action= new GetProjectIterationsAction($project);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
