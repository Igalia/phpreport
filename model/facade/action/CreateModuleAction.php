<?php

/** File for CreateModuleAction
 *
 *  This file just contains {@link CreateModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ModuleVO.php');

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
