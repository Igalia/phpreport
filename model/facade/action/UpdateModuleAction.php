<?php

/** File for UpdateModuleAction
 *
 *  This file just contains {@link UpdateModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ModuleVO.php');

/** Update Module Action
 *
 *  This action is used for updating an Module.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateModuleAction extends Action{

    /** The Module
     *
     * This variable contains the Module we want to update.
     *
     * @var ModuleVO
     */
    private $module;

    /** UpdateModuleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param ModuleVO $module the Module value object we want to update.
     */
    public function __construct(ModuleVO $module) {
        $this->module=$module;
        $this->preActionParameter="UPDATE_MODULE_PREACTION";
        $this->postActionParameter="UPDATE_MODULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Module on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getModuleDAO();
        if ($dao->update($this->module)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$modulevo= new ModuleVO();
$modulevo->setId(1);
$modulevo->setName('Pizza Deliverers');
$modulevo->setInit(date_create('2009-04-01'));
$modulevo->setEnd(date_create('2009-04-08'));
$modulevo->setSummary("Bad news girls!");
$modulevo->setProjectId(2);
$action= new UpdateModuleAction($modulevo);
var_dump($action);
$action->execute();
var_dump($modulevo);
*/
