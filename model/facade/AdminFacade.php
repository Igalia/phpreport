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


/** File for AdminFacade
 *
 *  This file just contains {@link AdminFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/CreateCommonEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteCommonEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateCommonEventAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCommonEventsByCityIdAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllCitiesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateCityAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteCityAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateCityAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CopyCityPreviousHolidaysAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllAreasAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateAreaAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteAreaAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateAreaAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/CommonEventVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');

/** Administration Facade
 *
 *  This Facade contains the functions used in Administration tasks.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class AdminFacade {

    /** Create Common Event Function
     *
     *  This function is used for creating a new Common Event.
     *
     * @param CommonEventVO $commonEvent the Common Event value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateCommonEvent(CommonEventVO $commonEvent) {

    $action = new CreateCommonEventAction($commonEvent);

    return $action->execute();

    }

    /** Delete Common Event Function
     *
     *  This function is used for deleting a Common Event.
     *
     * @param CommonEventVO $commonEvent the Common Event value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteCommonEvent(CommonEventVO $commonEvent) {

    $action = new DeleteCommonEventAction($commonEvent);

    return $action->execute();

    }

    /** Update Common Event Function
     *
     *  This function is used for updating a Common Event.
     *
     * @param CommonEventVO $commonEvent the Common Event value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateCommonEvent(CommonEventVO $commonEvent) {

    $action = new UpdateCommonEventAction($commonEvent);

    return $action->execute();

    }

    /** Get Common Events by City Id Function
     *
     *  This function is used for retrieving the CommonEvent objects for a specific city.
     *
     * @param int Id of the city to filter the CommonEvents
     * @throws {@link SQLQueryErrorException}
     */
    static function GetCommonEventsByCityId($cityId) {

        $action = new GetCommonEventsByCityIdAction($cityId);

        return $action->execute();

    }

    /** Get all Cities Function
     *
     *  This action is used for retrieving all Cities.
     *
     * @return array an array with value objects {@link CityVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetAllCities() {

    $action = new GetAllCitiesAction();

    return $action->execute();

    }

    /** Create City Function
     *
     *  This function is used for creating a new City.
     *
     * @param CityVO $city the City value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateCity(CityVO $city) {

    $action = new CreateCityAction($city);

    return $action->execute();

    }

    /** Delete City Function
     *
     *  This function is used for deleting a City.
     *
     * @param CityVO $city the City value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteCity(CityVO $city) {

    $action = new DeleteCityAction($city);

    return $action->execute();

    }

    /** Update City Function
     *
     *  This function is used for updating a City.
     *
     * @param CityVO $city the City value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateCity(CityVO $city) {

    $action = new UpdateCityAction($city);

    return $action->execute();

    }

    /** Copy previous year holidays for a city Function
     *
     *  This function is used for copying previous year holidays for a city.
     *
     * @param int $cityId the City identifier.
     * @param int $year the year we want to copy the holidays from.
     * @return int the number of holiday entries copied.
     */
    static function CopyCityPreviousHolidays(CityVO $city, $year) {

    $action = new CopyCityPreviousHolidaysAction($city, $year);

    return $action->execute();

    }

    /** Get all Areas Function
     *
     *  This action is used for retrieving all Areas.
     *
     * @return array an array with value objects {@link AreaVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetAllAreas() {

    $action = new GetAllAreasAction();

    return $action->execute();

    }

    /** Create Area Function
     *
     *  This function is used for creating a new Area.
     *
     * @param AreaVO $area the Area value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateArea(AreaVO $area) {

    $action = new CreateAreaAction($area);

    return $action->execute();

    }

    /** Delete Area Function
     *
     *  This function is used for deleting an Area.
     *
     * @param AreaVO $area the Area value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteArea(AreaVO $area) {

    $action = new DeleteAreaAction($area);

    return $action->execute();

    }

    /** Update Area Function
     *
     *  This function is used for updating an Area.
     *
     * @param AreaVO $area the Area value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateArea(AreaVO $area) {

    $action = new UpdateAreaAction($area);

    return $action->execute();

    }

    /** Synchronize data Function
     *
     *  This function is used for synchronizing data on DB and LDAP. It can synchronize data of only a user, or for all them if we don't pass one.
     *
     * @param UserVO $user the User whose data we want to synchronize.
     * @return int number of entries that have changed.
     */
    static function SynchronizeData(UserVO $user = NULL) {

    $action = new SynchronizeDataAction($user);

    return $action->execute();

    }

}
