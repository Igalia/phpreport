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


/** File for CreateSectionAction
 *
 *  This file just contains {@link CreateSectionAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');

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
