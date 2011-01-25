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


/** File for GetProjectExtraDataAction
 *
 *  This file just contains {@link GetProjectExtraData}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');


/** Get Project Extra Data Action
 *
 *  This action is used for retrieving extra data about a Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectExtraDataAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose extra data we want to retieve.
     *
     * @var int
     */
    private $projectId;

    /** GetProjectExtraDataAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $projectId the id of Project whose extra data we want to retieve.
     */
    public function __construct($projectId) {
        $this->projectId=$projectId;
        $this->preActionParameter="GET_PROJECT_EXTRA_DATA_PREACTION";
        $this->postActionParameter="GET_PROJECT_EXTRA_DATA_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that computes the project's extra data.
     *
     * @return array an array with extra data as associative fields 'total' and 'currentInvoice'.
     */
    protected function doExecute() {

        $dao1 = DAOFactory::getTaskDAO();

        $dao2 = DAOFactory::getProjectDAO();

        $tasks = $dao1->getByProjectId($this->projectId);

        $project = $dao2->getById($this->projectId);

        $results[total] = 0;

        foreach((array)$tasks as $task)
            $results[total] += ($task->getEnd() - $task->getInit())/60;

        if ((!$project->getActivation()) || ($project->getEstHours() < $results[total]))
            $total = $results[total];
        else
            $total = $project->getEstHours();

        $results[currentInvoice] = $project->getInvoice()/$total;

        return $results;

    }

}


/*//Test code;

$action= new GetProjectExtraDataAction(138);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
