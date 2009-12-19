<?php

/** File for GetModuleSectionsAction
 *
 *  This file just contains {@link GetModuleSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ModuleVO.php');


/** Get Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetModuleSectionsAction extends Action{

    /** The Project Id
     *
     * This variable contains the id of the Project whose Sections we want to retieve.
     *
     * @var int
     */
    private $moduleId;

    /** GetModuleSectionsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $moduleId the id of the Module whose Sections we want to retieve.
     */
    public function __construct($moduleId) {
        $this->moduleId=$moduleId;
        $this->preActionParameter="GET_MODULE_SECTIONS_PREACTION";
        $this->postActionParameter="GET_ITERATION_STORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Sections from persistent storing.
     *
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getSectionDAO();

        return $dao->getByModuleId($this->moduleId);

    }

}


/*//Test code;

$action= new GetModuleSectionsAction(8);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
