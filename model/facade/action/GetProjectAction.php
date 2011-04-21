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


/** File for GetProjectAction
 *
 *  This file just contains {@link GetProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get Project Action
 *
 *  This action is used for retrieving an Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetProjectAction extends Action{

    /** The Project id
     *
     * This variable contains the id of the Project we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Project we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_PROJECT_PREACTION";
        $this->postActionParameter="GET_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Project from persistent storing.
     *
     * @return ProjectVO the Project as a {@link ProjectVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetProjectAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
