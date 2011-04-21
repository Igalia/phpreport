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


/** File for GetCustomSectionAction
 *
 *  This file just contains {@link GetCustomSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/GetModuleCustomSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskSectionVO.php');


/** Get Custom Section Action
 *
 *  This action is used for retrieving a Custom Section by it's id.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetCustomSectionAction extends GetModuleCustomSectionsAction{

    /** The Section Id
     *
     * This variable contains the id of the Custom Section we want to retieve.
     *
     * @var int
     */
    private $sectionId;

    /** GetCustomSectionAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $sectionId the id of the Custom Section we want to retieve.
     */
    public function __construct($sectionId) {
        $this->sectionId=$sectionId;
        $this->preActionParameter="GET_CUSTOM_SECTION_PREACTION";
        $this->postActionParameter="GET_CUSTOM_SECTION_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Section from persistent storing.
     *
     * @return CustomSectionVO a custom object {@link CustomSectionVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    protected function doExecute() {

    $dao = DAOFactory::getSectionDAO();

    $sections[] = $dao->getById($this->sectionId);

    if ($sections[0] == NULL)
        return NULL;

    $customSections = $this->SectionsToCustomSections($sections);

    return $customSections[0];

    }

}


/*//Test code;

$action= new GetCustomSectionAction(1);
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
