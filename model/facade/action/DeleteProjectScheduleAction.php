<?php

/** File for DeleteProjectScheduleAction
 *
 *  This file just contains {@link DeleteProjectScheduleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectScheduleVO.php');

/** Delete Project Schedule Action
 *
 *  This action is used for deleting a Project Schedule.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteProjectScheduleAction extends Action{

    /** The Project Schedule
     *
     * This variable contains the Project Schedule we want to delete.
     *
     * @var ProjectScheduleVO
     */
    private $projectSchedule;

    /** DeleteProjectScheduleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectScheduleVO $project the Project Schedule value object we want to delete.
     */
    public function __construct(ProjectScheduleVO $projectSchedule) {
        $this->projectSchedule=$projectSchedule;
        $this->preActionParameter="DELETE_PROJECT_SCHEDULE_PREACTION";
        $this->postActionParameter="DELETE_PROJECT_SCHEDULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Project Schedule from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectScheduleDAO();
        if ($dao->delete($this->projectSchedule)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$projectschedulevo= new ProjectScheduleVO();
$projectschedulevo->setId(1);
$action= new DeleteProjectScheduleAction($projectschedulevo);
var_dump($action);
$action->execute();
var_dump($projectschedulevo);
*/
