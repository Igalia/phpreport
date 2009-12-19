<?php

/** File for GetProjectAction
 *
 *  This file just contains {@link GetProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Project Action
 *
 *  This action is used for retrieving an Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Project we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_PROJECT_PREACTION";
        $this->postActionParameter="GET_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Project from persistent storing.
     *
     * @return ProjectVO the Project as a {@link ProjectVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetProjectAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
