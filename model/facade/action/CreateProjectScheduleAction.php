<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for CreateProjectScheduleAction
 *
 *  This file just contains {@link CreateProjectScheduleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectScheduleVO.php');

/** Create Project Schedule Action
 *
 *  This action is used for creating a new Project Schedule.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateProjectScheduleAction extends Action{

    /** The Project Schedule
     *
     * This variable contains the Project Schedule we want to create.
     *
     * @var ProjectScheduleVO
     */
    private $project;

    /** CreateProjectScheduleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectScheduleVO $projectSchedule the Project Schedule value object we want to create.
     */
    public function __construct(ProjectScheduleVO $projectSchedule) {
        $this->projectSchedule=$projectSchedule;
        $this->preActionParameter="CREATE_PROJECT_SCHEDULE_PREACTION";
        $this->postActionParameter="CREATE_PROJECT_SCHEDULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Project Schedule, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectScheduleDAO();
        if ($dao->create($this->projectSchedule)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$projectschedulevo->setWeeklyLoad(25.5);
$projectschedulevo->setInitWeek(12);
$projectschedulevo->setInitYear(2005);
$projectschedulevo->setEndWeek(9);
$projectschedulevo->setEndYear(2006);
$projectschedulevo->setUserId(1);
$projectschedulevo->setProjectId(1);

$action= new CreateProjectScheduleAction($projectschedulevo);
var_dump($action);
$action->execute();
var_dump($projectschedulevo);
*/
