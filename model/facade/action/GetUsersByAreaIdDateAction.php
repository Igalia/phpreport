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


/** File for GetUsersByAreaIdDateAction
 *
 *  This file just contains {@link GetUsersByAreaIdDateAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get Users By Area Id And Date Action
 *
 *  This action is used for retrieving all Users that are assigned to an
 *  Area on a specific date.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class GetUsersByAreaIdDateAction extends Action{

    /** The Area id
     *
     * This variable contains the id of the Area whose related Users we want to retieve.
     *
     * @var int
     */
    private $areaId;

    /** The Date
     *
     * This variable contains the date when we want to check the Area assignment.
    *
     * @var DateTime
     */
    private $date;

    /** GetUsersByAreaIdDateAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param int $areaId the database identifier of the Area whose related
     * Users we want to retieve.
     * @param DateTime $date the date when we want to check the Area
     * assignment.
     */
    public function __construct($areaId, DateTime $date) {
        $this->areaId = $areaId;
        $this->date = $date;
        $this->preActionParameter="GET_USERS_BY_AREA_ID_DATE_PREACTION";
        $this->postActionParameter="GET_USERS_BY_AREA_ID_DATE_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Users from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

    $dao = DAOFactory::getUserDAO();

        return $dao->getByAreaDate($this->areaId, $this->date);

    }

}


/*//Test code;

$action= new GetUsersByAreaIdDateAction(2, new DateTime());
var_dump($action);
$result = $action->execute();
var_dump($result);
*/
