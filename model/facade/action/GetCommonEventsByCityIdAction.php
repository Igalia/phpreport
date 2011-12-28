<?php
/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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


/** File for GetCommonEventsByCityIdAction
 *
 *  This file just contains {@link GetCommonEventsByCityIdAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');


/** Get Common Events by City Id Action.
 *
 *  This action is used for retrieving the CommonEvent objects for a specific city.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
class GetCommonEventsByCityIdAction extends Action{

    private $cityId;
    /** GetAllCitiesAction constructor.
     *
     * This is just the constructor of this action.
     */
    public function __construct($cityId) {
        $this->preActionParameter="GET_COMMON_EVENTS_BY_CITY_ID_PREACTION";
        $this->postActionParameter="GET_COMMON_EVENTS_BY_CITY_ID_POSTACTION";

        $this->cityId = $cityId;
    }

    /** Specific code execute.
     *
     * This is the function that contains the code that retrieves the Cities from persistent storing.
     *
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    protected function doExecute() {

        $dao = DAOFactory::getCommonEventDAO();
        return $dao->getByCityId($this->cityId);
    }

}
