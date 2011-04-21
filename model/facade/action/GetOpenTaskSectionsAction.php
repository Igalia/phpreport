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


/** File for GetOpenTaskSectionsAction
 *
 *  This file just contains {@link GetOpenTaskSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** Get Open Task Sections Action
 *
 *  This action is used for retrieving open Task Sections.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetOpenTaskSectionsAction extends Action{

    /** Project Id
     *
     * This variable contains the optional parameter for retrieving only open Task Sections related to a Project.
     *
     * @var int
     */
    private $projectId;

    /** User Id
     *
     * This variable contains the optional parameter for retrieving only open Task Sections related to an User.
     *
     * @var int
     */
    private $userId;

    /** GetActiveTaskSectionsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $userId optional parameter for filtering by User.
     * @param int $projectId optional parameter for filtering by Project.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    public function __construct($userId = NULL, $projectId = NULL) {

        $this->preActionParameter="GET_OPEN_TASK_SECTIONS_PREACTION";
        $this->postActionParameter="GET_OPEN_TASK_SECTIONS_POSTACTION";
        $this->userId = $userId;
        $this->projectId = $projectId;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns all Projects.
     *
     * @return array an array with all the existing Projects.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskSectionDAO();

    return $dao->getOpen($this->userId, $this->projectId);

    }

}


/*//Test code;

$action= new GetOpenTaskSectionsAction();
//var_dump($action);
$result = $action->execute();
var_dump($result);*/
