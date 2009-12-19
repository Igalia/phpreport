<?php

/** File for GetModuleCustomSectionsAction
 *
 *  This file just contains {@link GetModuleCustomSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/GetSectionCustomTaskSectionsAction.php');
include_once('phpreport/model/facade/action/GetUserAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ModuleVO.php');
include_once('phpreport/model/vo/CustomSectionVO.php');
include_once('phpreport/model/vo/CustomTaskSectionVO.php');


/** Get Module Custom Sections Action
 *
 *  This action is used for retrieving all Custom Sections (Sections with additional data) related to an Module.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetModuleCustomSectionsAction extends GetSectionCustomTaskSectionsAction{

    /** The Module Id
     *
     * This variable contains the id of the Module whose Custom Sections we want to retieve.
     *
     * @var int
     */
    private $moduleId;

    /** GetModuleCustomSectionsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $moduleId the id of the Module whose Custom Sections we want to retieve.
     */
    public function __construct($moduleId) {
        $this->moduleId=$moduleId;
        $this->preActionParameter="GET_MODULE_CUSTOM_SECTIONS_PREACTION";
        $this->postActionParameter="GET_MODULE_CUSTOM_SECTIONS_POSTACTION";

    }

    /** SectionsToCustomSections function.
     *
     * This function receives an array of value objects {@link SectionVO} and creates their custom objects {@link CustomSectionVO}.
     *
     * @param array $stories an array of value objects {@link SectionVO}.
     * @return array an array with custom objects {@link CustomSectionVO} with their properties set to the values from the value
     * objects {@link SectionVO} and with additional data and ordered ascendantly by their database internal identifier
     */
    protected function SectionsToCustomSections($stories) {

    $customSections = array();

    foreach ((array) $stories as $story)
    {

        $customSection = new CustomSectionVO();

        $customSection->setName($story->getName());

        $customSection->setText($story->getText());

        $customSection->setId($story->getId());

        $customSection->setAccepted($story->getAccepted());

        $customSection->setModuleId($story->getModuleId());

        $spent = 0.0;

        $toDo = 0.0;

        $estHours = 0.0;

        $developers = array();

        $dao = DAOFactory::getTaskSectionDAO();

        $taskSections = $dao->getBySectionId($customSection->getId());

        $customTaskSections = $this->TaskSectionsToCustomTaskSections($taskSections);

        if (!is_null($story->getUserId()))
        {

            $action = new getUserAction($story->getUserId());

            $customSection->setReviewer($action->execute());

        }

        foreach($customTaskSections as $taskSection)
        {

            $spent += $taskSection->getSpent();
            $toDo += $taskSection->getToDo();
            $estHours += $taskSection->getEstHours();
            $developer = $taskSection->getDeveloper();
            if (!is_null($developer))
                $developers[$developer->getLogin()] = $developer;

        }

        $customSection->setSpent($spent);

        $customSection->setEstHours($estHours);

        $customSection->setToDo($toDo);

        $customSection->setDevelopers($developers);

        $customSection->setDone($spent/($spent+$toDo));

        if ($estHours)
            $customSection->setOverrun((($spent+$toDo)/$estHours) - 1.0);

        $customSections[] = $customSection;

    }

    return $customSections;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Sections from persistent storing.
     *
     * @return array an array with custom objects {@link CustomSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectionDAO();

    $stories = $dao->getByModuleId($this->moduleId);

    return $this->SectionsToCustomSections($stories);

    }

}


/*//Test code;

$action= new GetModuleCustomSectionsAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
