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


/** File for GetAllProjectsAction
 *
 *  This file just contains {@link GetAllProjectsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

/** Get All Projects Action
 *
 *  This action is used for retrieving all Projects.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetAllProjectsAction extends Action{

    /** Active projects flag
     *
     * This variable contains the optional parameter for retrieving only active projects.
     *
     * @var bool
     */
    private $active;

    /** Order field
     *
     * This variable contains the optional parameter for ordering value objects by a specific field.
     *
     * @var string
     */
    private $order;

    /** GetAllProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     */
    public function __construct($active = False, $order = 'id') {
        $this->preActionParameter="GET_ALL_PROJECTS_PREACTION";
        $this->postActionParameter="GET_ALL_PROJECTS_POSTACTION";
        $this->active = $active;
        $this->order = $order;
    }

    /** Specific code execute.
     *
     * This is the function that contains the code that returns all Projects.
     *
     * @return array an array with all the existing Projects.
     */
    protected function doExecute() {
        $dao = DAOFactory::getProjectDAO();
        return $dao->getAll($this->active, $this->order);
    }

}


/*//Test code;

$action= new GetAllProjectsAction(True);
//var_dump($action);
$result = $action->execute();
var_dump($result);*/
