<?php

/** File for CreateSectionAction
 *
 *  This file just contains {@link CreateSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/Action.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/SectionVO.php');

/** Create Section Action
 *
 *  This action is used for creating a new Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateSectionAction extends Action{

    /** The Section
     *
     * This variable contains the Section we want to create.
     *
     * @var SectionVO
     */
    private $project;

    /** CreateSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param SectionVO $section the Section value object we want to create.
     */
    public function __construct(SectionVO $section) {
        $this->section=$section;
        $this->preActionParameter="CREATE_SECTION_PREACTION";
        $this->postActionParameter="CREATE_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Section, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectionDAO();
        if ($dao->create($this->section)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$sectionvo = new SectionVO;

$sectionvo->setName("Bring the crates");
$sectionvo->setAccepted(False);
$sectionvo->setModuleId(1);
$sectionvo->setUserId(1);

$action= new CreateSectionAction($sectionvo);
var_dump($action);
$action->execute();
var_dump($sectionvo);*/
