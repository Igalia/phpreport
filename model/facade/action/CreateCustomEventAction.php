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


/** File for CreateCustomEventAction
 *
 *  This file just contains {@link CreateCustomEventAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomEventVO.php');

/** Create Custom Event Action
 *
 *  This action is used for creating a new Custom Event.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCustomEventAction extends Action{

    /** The Custom Event
     *
     * This variable contains the Custom Event we want to create.
     *
     * @var CustomEventVO
     */
    private $customEvent;

    /** CreateCustomEventAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CustomEventVO $customEvent the Custom Event value object we want to create.
     */
    public function __construct(CustomEventVO $customEvent) {
        $this->customEvent=$customEvent;
        $this->preActionParameter="CREATE_CUSTOM_EVENT_PREACTION";
        $this->postActionParameter="CREATE_CUSTOM_EVENT_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new Custom Event, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCustomEventDAO();

        if ($dao->create($this->customEvent)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$customeventvo = new customEventVO();

$customeventvo->setDate(date_create('2005-12-21'));
$customeventvo->setUserId(1);
$customeventvo->setHours(3);
$customeventvo->setType("Ctrl+Alt+Supr");

$action= new CreateCustomEventAction($customeventvo);
var_dump($action);
$action->execute();
var_dump($customeventvo);
*/
