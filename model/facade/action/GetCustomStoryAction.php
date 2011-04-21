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


/** File for GetCustomStoryAction
 *
 *  This file just contains {@link GetCustomStoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/GetIterationCustomStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskStoryVO.php');


/** Get Custom Story Action
 *
 *  This action is used for retrieving a Custom Story by it's id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomStoryAction extends GetIterationCustomStoriesAction{

    /** The Story Id
     *
     * This variable contains the id of the Custom Story we want to retieve.
     *
     * @var int
     */
    private $storyId;

    /** GetCustomStoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $storyId the id of the Custom Story we want to retieve.
     */
    public function __construct($storyId) {
        $this->storyId=$storyId;
        $this->preActionParameter="GET_CUSTOM_STORY_PREACTION";
        $this->postActionParameter="GET_CUSTOM_STORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Story from persistent storing.
     *
     * @return CustomStoryVO a custom object {@link CustomStoryVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    protected function doExecute() {

    $dao = DAOFactory::getStoryDAO();

    $stories[] = $dao->getById($this->storyId);

    if ($stories[0] == NULL)
        return NULL;

    $customStories = $this->StoriesToCustomStories($stories);

    return $customStories[0];

    }

}


/*//Test code;

$action= new GetCustomStoryAction(2);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
