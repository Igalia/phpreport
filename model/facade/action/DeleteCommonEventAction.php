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


/** File for DeleteCommonEventAction
 *
 *  This file just contains {@link DeleteCommonEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CommonEventVO.php');

/** Delete Common Event Action
 *
 *  This action is used for deleting a Common Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteCommonEventAction extends Action{

    /** The Common Event
     *
     * This variable contains the Common Event we want to delete.
     *
     * @var CommonEventVO
     */
    private $commonEvent;

    /** DeleteCommonEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CommonEventVO $commonEvent the Common Event value object we want to delete.
     */
    public function __construct(CommonEventVO $commonEvent) {
        $this->commonEvent=$commonEvent;
        $this->preActionParameter="DELETE_COMMON_EVENT_PREACTION";
        $this->postActionParameter="DELETE_COMMON_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Common Event from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

        $dao = DAOFactory::getCommonEventDAO();
        if ($dao->delete($this->commonEvent)!=1) {
            return -1;
        }

        return 0;
    }

}

/*
//Test code
$commoneventvo = new commonEventVO();

$commoneventvo->setId(1);

$action= new DeleteCommonEventAction($commoneventvo);
var_dump($action);
$action->execute();
var_dump($commoneventvo);
*/
