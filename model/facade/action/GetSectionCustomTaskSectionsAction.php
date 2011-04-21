<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
 *
 * This file is part of PhpReport.
 *
 * PhpReport is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpReport is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpReport.  If not, see <http://www.gnu.org/licenses/>.
 */


/** File for GetSectionCustomTaskSectionsAction
 *
 *  This file just contains {@link GetSectionTaskSectionsAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskSectionVO.php');


/** Get Section Task Sections Action
 *
 *  This action is used for retrieving all custom Task Sections (Task Sections with additional data) related to a Section.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetSectionCustomTaskSectionsAction extends Action {

    /** The Section Id
     *
     * This variable contains the id of the Section whose custom Task Sections we want to retieve.
     *
     * @var int
     */
    private $sectionId;

    /** GetSectionCustomTaskSectionsAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $sectionId the id of the Section whose Task Sections we want to retieve.
     */
    public function __construct($sectionId) {
        $this->sectionId=$sectionId;
        $this->preActionParameter="GET_SECTION_CUSTOM_TASK_SECTIONS_PREACTION";
        $this->postActionParameter="GET_SECTION_CUSTOM_TASK_SECTIONS_POSTACTION";

    }

    /** TaskSectionsToCustomTaskSections function.
     *
     * This function receives an array of value objects {@link TaskSectionVO} and creates their custom objects {@link CustomTaskSectionVO}.
     *
     * @param array $taskSections an array of value objects {@link TaskSectionVO}.
     * @return array an array with custom objects {@link CustomTaskSectionVO} with their properties set to the values from the value
     * objects {@link TaskSectionVO} and with additional data and ordered ascendantly by their database internal identifier
     */
    protected function TaskSectionsToCustomTaskSections($taskSections) {

    $customTaskSections = array();

    foreach ((array) $taskSections as $taskSection)
    {

        $customTaskSection = new CustomTaskSectionVO();

        $customTaskSection->setName($taskSection->getName());

        $customTaskSection->setId($taskSection->getId());

        $customTaskSection->setRisk($taskSection->getRisk());

        $customTaskSection->setEstHours($taskSection->getEstHours());

        $customTaskSection->setSectionId($taskSection->getSectionId());

        if (!is_null($taskSection->getUserId()))
        {

            $dao = DAOFactory::getUserDAO();

            $customTaskSection->setDeveloper($dao->getById($taskSection->getUserId()));

        }

        if (!is_null($taskSection->getSectionId()))
        {

            $dao = DAOFactory::getSectionDAO();

            $section = $dao->getById($taskSection->getSectionId());

            if (!is_null($section->getUserId()))
            {

                $dao = DAOFactory::getUserDAO();

                $customTaskSection->setReviewer($dao->getById($section->getUserId()));

            }

        }

        $dao = DAOFactory::getTaskStoryDAO();

        $taskStories = $dao->getByTaskSectionId($taskSection->getId());

        $spent = 0.0;

        foreach((array) $taskStories as $taskStory)
            $spent += $taskStory->getEstHours();


        $customTaskSection->setSpent($spent);

        $customTaskSection->setToDo($taskSection->getEstHours() - $spent);

        $customTaskSections[] = $customTaskSection;

    }

    return $customTaskSections;

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Task Sections from persistent storing and calls the function
     * that creates the Custom Objects.
     *
     * @return array an array with custom objects {@link CustomTaskSectionVO} with their properties set to the values from the rows
     * and with additional data and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getTaskSectionDAO();

    $taskSections = $dao->getBySectionId($this->sectionId);

    return $this->TaskSectionsToCustomTaskSections($taskSections);

    }

}


/*//Test code;

$action= new GetSectionCustomTaskSectionsAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
