<?php

/** File for GetSectionAction
 *
 *  This file just contains {@link GetSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');


/** Get Section Action
 *
 *  This action is used for retrieving a Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetSectionAction extends Action{

    /** The Section id
     *
     * This variable contains the id of the Section we want to retieve.
     *
     * @var int
     */
    private $id;

    /** GetSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $id the database identifier of the Section we want to retieve.
     */
    public function __construct($id) {
        $this->id=$id;
        $this->preActionParameter="GET_SECTION_PREACTION";
        $this->postActionParameter="GET_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Section from persistent storing.
     *
     * @return SectionVO the Section as a {@link SectionVO} with its properties set to the values from the row.
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectionDAO();

        return $dao->getById($this->id);

    }

}


/*//Test code;

$action= new GetSectionAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
