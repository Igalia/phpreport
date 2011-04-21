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


/** File for CreateCityAction
 *
 *  This file just contains {@link CreateCityAction}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityVO.php');

/** Create City Action
 *
 *  This action is used for creating a new City.
 *
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
class CreateCityAction extends Action{

    /** The City
     *
     * This variable contains the City we want to create.
     *
     * @var CityVO
     */
    private $area;

    /** CreateCityAction constructor.
     *
     * This is just the constructor of this action.
     *
     * @param CityVO $city the City value object we want to create.
     */
    public function __construct(CityVO $city) {
        $this->city=$city;
        $this->preActionParameter="CREATE_CITY_PREACTION";
        $this->postActionParameter="CREATE_CITY_POSTACTION";

    }

    /** Specific code execute.
     *
     * This is the function that contains the code that creates the new City, storing it persistently.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    protected function doExecute() {

    $dao = DAOFactory::getCityDAO();
        if ($dao->create($this->city)!=1) {
            return -1;
        }

        return 0;
    }

}


/*//Test code

$cityvo = new CityVO();
$cityvo->setName('New New York');
$action= new CreateCityAction($cityvo);
var_dump($action);
$action->execute();
var_dump($cityvo);
*/
