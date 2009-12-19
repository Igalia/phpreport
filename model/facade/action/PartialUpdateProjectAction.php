<?php

/** File for PartialUpdateProjectAction
 *
 *  This file just contains {@link PartialUpdateProjectAction}.
 *
 * @filesource
 * @package PhpProject
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');

/** Partial Update Project Action
 *
 *  This action is used for updating only some fields of a Project.
 *
 * @package PhpProject
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class PartialUpdateProjectAction extends Action{

    /** The Project
     *
     * This variable contains the Project we want to update.
     *
     * @var ProjectVO
     */
    private $project;

    /** The flags array
     *
     * This variable contains flags indicating which fields we want to update.
     *
     * @var array
     */
    private $update;

    /** PartialUpdateProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectVO $project the Project value object we want to update.
     */
    public function __construct(ProjectVO $project, $update) {
        $this->project=$project;
        $this->update=$update;
        $this->preActionParameter="PARTIAL_UPDATE_PROJECT_PREACTION";
        $this->postActionParameter="PARTIAL_UPDATE_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Project on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getProjectDAO();

        if ($dao->partialUpdate($this->project, $this->update)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code
$projectvo = new ProjectVO();

$projectvo->setId(1);

$projectvo->setActivation(false);
$projectvo->setInit(date_create('2010-04-23'));
$projectvo->setEnd(date_create('2010-04-25'));
$projectvo->setDescription("Very bad :'(");
$projectvo->setInvoice(2345);
$projectvo->setEstHours(15);
$projectvo->setType("Chorrada test");
$projectvo->setMovedHours(15);
$projectvo->setSchedType("Testing type");
$projectvo->setAreaId(1);

$update[description] = true;

$action= new PartialUpdateProjectAction($projectvo, $update);
var_dump($action);
$action->execute();
var_dump($projectvo);*/
