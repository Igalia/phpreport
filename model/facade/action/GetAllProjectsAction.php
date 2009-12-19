<?php

/** File for GetAllProjectsAction
 *
 *  This file just contains {@link GetAllProjectsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');

/** Get All Projects Action
 *
 *  This action is used for retrieving all Projects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllProjectsAction extends Action{

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only active projects.
     *
     * @var bool
     */
    private $active;

    /** GetAllProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     */
    public function __construct($active = False) {
        $this->preActionParameter="GET_ALL_PROJECTS_PREACTION";
        $this->postActionParameter="GET_ALL_PROJECTS_POSTACTION";
    $this->active = $active;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns all Projects.
     *
     * @return array an array with all the existing Projects.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

    return $dao->getAll($this->active);

    }

}


/*//Test code;

$action= new GetAllProjectsAction(True);
//var_dump($action);
$result = $action->execute();
var_dump($result);*/
