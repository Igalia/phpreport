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


/** File for GetProjectModulesAction
 *
 *  This file just contains {@link GetProjectModulesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');


/** Get Project Modules Action
 *
 *  This action is used for retrieving all Modules related to a Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectModulesAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Modules we want to retieve.
     *
     * @var int
     */
    private $projectId;

    /** GetProjectModulesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $projectId the id of Project whose Modules we want to retieve.
     */
    public function __construct($projectId) {
        $this->projectId=$projectId;
        $this->preActionParameter="GET_PROJECT_MODULES_PREACTION";
        $this->postActionParameter="GET_PROJECT_MODULES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Modules from persistent storing.
     *
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getModuleDAO();

        return $dao->getByProjectId($this->projectId);

    }

}


/*//Test code;

$dao = DAOFactory::getProjectDAO();

$project = $dao->getById(1);

$action= new GetProjectModulesAction($project);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
