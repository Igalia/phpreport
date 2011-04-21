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


/** File for CreateModuleAction
 *
 *  This file just contains {@link CreateModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');

/** Create Module Action
 *
 *  This action is used for creating a new Module.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateModuleAction extends Action{

    /** The Module
     *
     * This variable contains the Module we want to create.
     *
     * @var ModuleVO
     */
    private $project;

    /** CreateModuleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ModuleVO $module the Module value object we want to create.
     */
    public function __construct(ModuleVO $module) {
        $this->module=$module;
        $this->preActionParameter="CREATE_MODULE_PREACTION";
        $this->postActionParameter="CREATE_MODULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Module, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getModuleDAO();
        if ($dao->create($this->module)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$modulevo = new ModuleVO;

$modulevo->setName("Qwark delivery");
$modulevo->setInit(date_create('2009-06-01'));
$modulevo->setEnd(date_create('2009-06-08'));
$modulevo->setSummary("Bad news boys!");
$modulevo->setProjectId(1);

$action= new CreateModuleAction($modulevo);
var_dump($action);
$action->execute();
var_dump($modulevo);
*/
