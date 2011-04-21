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


/** File for CreateCityHistoryAction
 *
 *  This file just contains {@link CreateCityHistoryAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityHistoryVO.php');

/** Create City History entry Action
 *
 *  This action is used for creating a new entry on City History.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCityHistoryAction extends Action{

    /** The City History
     *
     * This variable contains the City History entry we want to create.
     *
     * @var CityHistoryVO
     */
    private $cityHistory;

    /** CreateCityHistoryAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CityHistoryVO $cityHistory the User value object we want to create.
     */
    public function __construct(CityHistoryVO $cityHistory) {
        $this->cityHistory=$cityHistory;
        $this->preActionParameter="CREATE_CITY_HISTORY_PREACTION";
        $this->postActionParameter="CREATE_CITY_HISTORY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new City History entry, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCityHistoryDAO();
        if ($dao->create($this->cityHistory)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$cityhistoryvo= new CityHistoryVO();
$cityhistoryvo->setCityId(1);
$cityhistoryvo->setUserId(1);
$cityhistoryvo->setInitDate(date_create("2009-01-01"));
$cityhistoryvo->setEndDate(date_create("2009-06-01"));
$action= new CreateCityHistoryAction($cityhistoryvo);
var_dump($action);
$action->execute();
var_dump($cityhistoryvo);
*/
