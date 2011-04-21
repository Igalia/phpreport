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


/** File for CreateProjectAction
 *
 *  This file just contains {@link CreateProjectAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

/** Create Project Action
 *
 *  This action is used for creating a new Project.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateProjectAction extends Action{

    /** The Project
     *
     * This variable contains the Project we want to create.
     *
     * @var ProjectVO
     */
    private $project;

    /** CreateProjectAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ProjectVO $project the Project value object we want to create.
     */
    public function __construct(ProjectVO $project) {
        $this->project=$project;
        $this->preActionParameter="CREATE_PROJECT_PREACTION";
        $this->postActionParameter="CREATE_PROJECT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Project, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getProjectDAO();
        if ($dao->create($this->project)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$projectvo = new ProjectVO();
$projectvo->setInit(date_create("1999-12-31"));
$projectvo->setAreaId(1);
$projectvo->setEnd(date_create("2999-12-31"));
$projectvo->setDescription("Good news, everyone!");
$projectvo->setActivation(TRUE);
$projectvo->setSchedType("Good news, everyone!");
$projectvo->setType("I've taught the toaster to feel love!");
$projectvo->setMovedHours(3.14);
$projectvo->setInvoice(5.55);
$projectvo->setEstHours(3.25);

$action= new CreateProjectAction($projectvo);
var_dump($action);
$action->execute();
var_dump($projectvo);
*/
