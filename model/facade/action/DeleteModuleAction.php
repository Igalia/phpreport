<?php

/** File for DeleteModuleAction
 *
 *  This file just contains {@link DeleteModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ModuleVO.php');

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
