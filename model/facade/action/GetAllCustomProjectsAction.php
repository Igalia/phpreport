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


/** File for GetAllCustomProjectsAction
 *
 *  This file just contains {@link GetAllCustomProjectsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** Get All Custom Projects Action
 *
 *  This action is used for retrieving all Projects as Custom Projects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllCustomProjectsAction extends Action{

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only active projects.
     *
     * @var bool
     */
    private $active;

    /** GetAllCustomProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     */
    public function __construct($active = False) {

        $this->preActionParameter="GET_ALL_CUSTOM_PROJECTS_PREACTION";
        $this->postActionParameter="GET_ALL_CUSTOM_PROJECTS_POSTACTION";
        $this->active = $active;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns all CustomProjects.
     *
     * @return array an array with all the existing CustomProjects.
     */
    protected function doExecute() {

        $dao = DAOFactory::getProjectDAO();

        return $dao->getAllCustom($this->active);;

    }

}


/*//Test code;

$action= new GetAllCustomProjectsAction(True);
//var_dump($action);
$result = $action->execute();
var_dump($result);
*/
