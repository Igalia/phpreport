<?php

/** File for GetModuleAction
 *
 *  This file just contains {@link GetModuleAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Module Action
 *
 *  This action is used for retrieving an Module.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetModuleAction extends Action{

    /** The Module id
     *
     * This variable contains the id of the Module we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetModuleAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Module we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_MODULE_PREACTION";
        $this->postActionParameter="GET_MODULE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Module from persistent storing.
     *
     * @return ModuleVO the Module as a {@link ModuleVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getModuleDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetModuleAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
