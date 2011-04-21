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


/** File for GetSectionTaskStoriesAction
 *
 *  This file just contains {@link GetSectionTaskStoriesAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');


/** Get Section Task Stories Action
 *
 *  This action is used for retrieving all Task Stories related to a Section through its Task Sections.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetSectionTaskStoriesAction extends Action{

    /** The Section Id
     *
     * This variable contains the id of the Section whose Task Stories we want to retieve.
     *
     * @var int
     */
    private $sectionId;

    /** GetSectionTaskStoriesAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $sectionId the id of the Section whose Task Stories we want to retieve.
     */
    public function __construct($sectionId) {
        $this->sectionId=$sectionId;
        $this->preActionParameter="GET_SECTION_TASK_STORIES_PREACTION";
        $this->postActionParameter="GET_SECTION_TASK_STORIES_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the TaskStories from persistent storing.
     *
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getTaskStoryDAO();

        return $dao->getBySectionId($this->sectionId);

    }

}


/*//Test code;

$action= new GetSectionTaskStoriesAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
