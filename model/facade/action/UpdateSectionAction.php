<?php

/** File for UpdateSectionAction
 *
 *  This file just contains {@link UpdateSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/SectionVO.php');

/** Update Section Action
 *
 *  This action is used for updating a Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateSectionAction extends Action{

    /** The Section
     *
     * This variable contains the Section we want to update.
     *
     * @var SectionVO
     */
    private $section;

    /** UpdateSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param SectionVO $section the Section value object we want to update.
     */
    public function __construct(SectionVO $section) {
        $this->section=$section;
        $this->preActionParameter="UPDATE_SECTION_PREACTION";
        $this->postActionParameter="UPDATE_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Section on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectionDAO();
        if ($dao->update($this->section)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$sectionvo= new SectionVO();
$sectionvo->setId(1);
$sectionvo->setName('Pizza Deliverers');
$action= new UpdateSectionAction($sectionvo);
var_dump($action);
$action->execute();
var_dump($sectionvo);
*/
