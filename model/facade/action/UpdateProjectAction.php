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


/** File for UpdateProjectAction
 *
 *  This file just contains {@link UpdateProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

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
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    protected function doExecute() {
        $dao = DAOFactory::getProjectDAO();
        return $dao->update($this->project);
    }

}
