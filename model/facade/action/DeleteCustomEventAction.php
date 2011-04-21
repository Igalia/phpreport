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


/** File for DeleteCustomEventAction
 *
 *  This file just contains {@link DeleteCustomEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomEventVO.php');

/** Delete Custom Event Action
 *
 *  This action is used for deleting a Custom Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class DeleteCustomEventAction extends Action{

    /** The Custom Event
     *
     * This variable contains the Custom Event we want to delete.
     *
     * @var CustomEventVO
     */
    private $customEvent;

    /** DeleteCustomEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomEventVO $CustomEvent the Custom Event value object we want to delete.
     */
    public function __construct(CustomEventVO $customEvent) {
        $this->customEvent=$customEvent;
        $this->preActionParameter="DELETE_CUSTOM_EVENT_PREACTION";
        $this->postActionParameter="DELETE_CUSTOM_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that deletes the Custom Event from persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomEventDAO();

        if ($dao->delete($this->customEvent)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$customeventvo = new customEventVO();

$customeventvo->setId(1);

$action= new DeleteCustomEventAction($customeventvo);
var_dump($action);
$action->execute();
var_dump($customeventvo);
*/
