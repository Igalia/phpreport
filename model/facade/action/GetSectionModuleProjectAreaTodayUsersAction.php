<?php

/** File for GetSectionModuleProjectAreaTodayUsersAction
 *
 *  This file just contains {@link GetSectionModuleProjectAreaTodayUsersAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Section Module Project Area Today Users Action
 *
 *  This action is used for retrieving all Users related to a Section Module Project Area today.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetSectionModuleProjectAreaTodayUsersAction extends Action{

    /** The Section Id
     *
     * @param int $sectionId the id of the Project Section whose related Users (through Area) we want to retrieve.
     *
     * @var int
     */
    private $sectionId;

    /** GetSectionProjectAreaTodayUsersAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $sectionId the id of the Project Section whose related Users (through Area) we want to retrieve.
     */
    public function __construct($sectionId) {
        $this->sectionId=$sectionId;
        $this->preActionParameter="GET_SECTION_MODULE_PROJECT_AREA_TODAY_USERS_PREACTION";
        $this->postActionParameter="GET_SECTION_MODULE_PROJECT_AREA_TODAY_USERS_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Users from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getUserDAO();

        return $dao->getBySectionModuleProjectAreaToday($this->sectionId);

    }

}


/*//Test code;

$action= new GetSectionModuleProjectAreaTodayUsersAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
