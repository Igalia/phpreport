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


/** File for DeleteProjectAction
 *
 *  This file just contains {@link DeleteProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

/** Delete Project Action
 *
 *  This action is used for deleting a Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteProjectAction extends Action{

    /** The Project
     *
     * This variable contains the Project we want to delete.
     *
     * @var ProjectVO
     */
    private $project;

    /** DeleteProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectVO $project the Project value object we want to delete.
     */
    public function __construct(ProjectVO $project) {
        $this->project=$project;
        $this->preActionParameter="DELETE_PROJECT_PREACTION";
        $this->postActionParameter="DELETE_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Project from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getProjectDAO();

        if ($dao->delete($this->project)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code

$projectvo= new ProjectVO();
$projectvo->setId(1);
$action= new DeleteProjectAction($projectvo);
var_dump($action);
$action->execute();
var_dump($projectvo);
*/
