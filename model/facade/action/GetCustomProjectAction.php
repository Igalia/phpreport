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


/** File for GetCustomProjectAction
 *
 *  This file just contains {@link GetCustomProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/CustomProjectVO.php');


/** Get Custom Project Action
 *
 *  This action is used for retrieving a Custom Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project whose Custom Project we want to retrieve.
     *
     * @var int
     */
    private $id;

    /** GetCustomProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Project whose Custom Project we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_CUSTOM_PROJECT_PREACTION";
        $this->postActionParameter="GET_CUSTOM_PROJECT_POSTACTION";

    }

   /** ProjectsToCustomProjects function.
     *
     * This function receives an array of value objects {@link ProjectVO} and creates their custom objects {@link CustomProjectVO}.
     *
     * @param array $projects an array of value objects {@link ProjectVO}.
     * @return array an array with custom objects {@link CustomProjectVO} with their properties set to the values from the value
     * objects {@link ProjectVO} and with additional data and ordered ascendantly by their database internal identifier
     */
    protected function ProjectsToCustomProjects($projects) {

        $customProjects = array();

        $taskDao = DAOFactory::getTaskDAO();

        $hourCostDao = DAOFactory::getHourCostHistoryDAO();

        foreach((array) $projects as $project) {

            $customProject = new CustomProjectVO();
            $customProject->setId($project->getId());
            $customProject->setActivation($project->getActivation());
            $customProject->setInit($project->getInit());
            $customProject->setEnd($project->getEnd());
            $customProject->setInvoice($project->getInvoice());
            $customProject->setEstHours($project->getEstHours());
            $customProject->setAreaId($project->getAreaId());
            $customProject->setType($project->getType());
            $customProject->setDescription($project->getDescription());
            $customProject->setMovedHours($project->getMovedHours());
            $customProject->setSchedType($project->getSchedType());

            $tasks = $taskDao->getByProjectId($project->getId());

            $timeSpent = 0;
            $moneySpent = 0;

            foreach ((array)$tasks as $task) {
                $hours =(($task->getEnd()-$task->getInit())/60);
                $timeSpent += $hours;
                $hourCost = $hourCostDao->getByIntervals($task->getDate(), $task->getDate(), $task->getUserId());
                if ($hourCost)
                    $moneySpent += $hours*($hourCost[0]->getHourCost());
            }

            $customProject->setWorkedHours($timeSpent);
            $customProject->setTotalCost($moneySpent);

            $customProjects[] = $customProject;

            unset($project);

        }

        return $customProjects;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Project from persistent storing and returns its Custom Project.
     *
     * @return CustomProjectVO the Project as a {@link CustomProjectVO} with its properties set to the values from the row and additional data.
     */
    protected function doExecute() {

        $dao = DAOFactory::getProjectDAO();

        $projects[] = $dao->getById($this->id);

        $customProjects = $this->ProjectsToCustomProjects($projects);

        return $customProjects[0];

    }

}


/*//Test code;

$action= new GetCustomProjectAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
print("PercDev: {$result->getPercDev()}\n");
print("AbsDev: {$result->getAbsDev()}\n");
print("EstHourInvoice: {$result->getEstHourInvoice()}\n");
print("TotalProfit: {$result->getTotalProfit()}\n");
print("HourProfit: {$result->getHourProfit()}\n");
print("WorkedHourInvoice: {$result->getWorkedHourInvoice()}\n");
*/
