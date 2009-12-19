<?php

/** File for UpdateProjectAction
 *
 *  This file just contains {@link UpdateProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');

/** Update Project Action
 *
 *  This action is used for updating a Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateProjectAction extends Action{

    /** The Project
     *
     * This variable contains the Project we want to update.
     *
     * @var ProjectVO
     */
    private $project;

    /** UpdateProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectVO $project the Project value object we want to update.
     */
    public function __construct(ProjectVO $project) {
        $this->project=$project;
        $this->preActionParameter="UPDATE_PROJECT_PREACTION";
        $this->postActionParameter="UPDATE_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that update the Project on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();
        if ($dao->update($this->project)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$projectvo = new ProjectVO();
$projectvo->setInit(date_create("2999-12-31"));
$projectvo->setAreaId(1);
$projectvo->setEnd(date_create("3000-12-31"));
$projectvo->setDescription("Good news, everyone!");
$projectvo->setActivation(TRUE);
$projectvo->setSchedType("Good news, everyone!");
$projectvo->setType("I've taught the toaster to feel love!");
$projectvo->setMovedHours(3.14);
$projectvo->setInvoice(5.55);
$projectvo->setEstHours(3.25);
$projectvo->setId(1);

$action= new UpdateProjectAction($projectvo);
var_dump($action);
$action->execute();
var_dump($projectvo);
*/
