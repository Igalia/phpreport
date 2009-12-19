<?php

/** File for UpdateProjectScheduleAction
 *
 *  This file just contains {@link UpdateProjectScheduleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectScheduleVO.php');

/** Update Project Schedule Action
 *
 *  This action is used for updating a Project Schedule.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateProjectScheduleAction extends Action{

    /** The Project Schedule
     *
     * This variable contains the Project Schedule we want to update.
     *
     * @var ProjectScheduleVO
     */
    private $projectSchedule;

    /** UpdateProjectScheduleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectScheduleVO $projectSchedule the Project Schedule value object we want to update.
     */
    public function __construct(ProjectScheduleVO $projectSchedule) {
        $this->projectSchedule=$projectSchedule;
        $this->preActionParameter="UPDATE_PROJECT_SCHEDULE_PREACTION";
        $this->postActionParameter="UPDATE_PROJECT_SCHEDULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that update the Project Schedule on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectScheduleDAO();
        if ($dao->update($this->projectSchedule)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$projectschedulevo->setWeeklyLoad(22.5);
$projectschedulevo->setInitWeek(10);
$projectschedulevo->setInitYear(2009);
$projectschedulevo->setEndWeek(3);
$projectschedulevo->setEndYear(2009);
$projectschedulevo->setUserId(1);
$projectschedulevo->setProjectId(1);
$projectschedulevo->setId(1);

$action= new UpdateProjectScheduleAction($projectschedulevo);
var_dump($action);
$action->execute();
var_dump($projectschedulevo);
*/
