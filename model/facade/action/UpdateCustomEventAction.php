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


/** File for UpdateCustomEventAction
 *
 *  This file just contains {@link UpdateCustomEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomEventVO.php');

/** Update Custom Event Action
 *
 *  This action is used for updating a Custom Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class UpdateCustomEventAction extends Action{

    /** The Custom Event
     *
     * This variable contains the Custom Event we want to update.
     *
     * @var CustomEventVO
     */
    private $customEvent;

    /** UpdateCustomEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to update.
     */
    public function __construct(CustomEventVO $customEvent) {
        $this->customEvent=$customEvent;
        $this->preActionParameter="UPDATE_CUSTOM_EVENT_PREACTION";
        $this->postActionParameter="UPDATE_CUSTOM_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that updates the Custom Event on persistent storing.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomEventDAO();

        if ($dao->update($this->customEvent)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$customeventvo = new customEventVO();

$customeventvo->setId(1);
$customeventvo->setDate(date_create('2007-12-21'));
$customeventvo->setUserId(1);
$customeventvo->setHours(3);
$customeventvo->setType("Blue screen!");

$action= new UpdateCustomEventAction($customeventvo);
var_dump($action);
$action->execute();
var_dump($customeventvo);
*/
