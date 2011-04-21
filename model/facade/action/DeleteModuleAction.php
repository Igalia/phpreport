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


/** File for DeleteModuleAction
 *
 *  This file just contains {@link DeleteModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');

/** Delete Module Action
 *
 *  This action is used for deleting an Module.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteModuleAction extends Action{

    /** The Module
     *
     * This variable contains the Module we want to delete.
     *
     * @var ModuleVO
     */
    private $module;

    /** DeleteModuleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ModuleVO $module the Module value object we want to delete.
     */
    public function __construct(ModuleVO $module) {
        $this->module=$module;
        $this->preActionParameter="DELETE_MODULE_PREACTION";
        $this->postActionParameter="DELETE_MODULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Module from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getModuleDAO();
        if ($dao->delete($this->module)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$modulevo= new ModuleVO();
$modulevo->setId(1);
$action= new DeleteModuleAction($modulevo);
var_dump($action);
$action->execute();
var_dump($modulevo);
*/
