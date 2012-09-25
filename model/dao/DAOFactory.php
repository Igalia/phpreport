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


/** File for DAOFactory
 *
 *  This file just contains {@link DAOFactory}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** Factory for obtaning DAOs
 *
 *  This class is used for obtaining new instances of every implemented DAO. It uses {@link ConfigurationParametersManager} for obtaining from {@link config.php} the
 *  implementation for the requested DAO, then creates a new instance and returns it.
 *
 * @package PhpReport
 * @subpackage DAO
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

class DAOFactory {


    /** Sector DAO creator
     *
     * This function returns a new instance of {@link SectorDAO}.
     *
     * @return SectorDAO a new instance of {@link SectorDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getSectorDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('SECTOR_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'SectorDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/SectorDAO/' . $className . ".php");
    return new $className;
  }

    /** User DAO creator
     *
     * This function returns a new instance of {@link UserDAO}.
     *
     * @return UserDAO a new instance of {@link UserDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getUserDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('USER_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'UserDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/' . $className . ".php");
    return new $className;
  }

    /** User Group DAO creator
     *
     * This function returns a new instance of {@link UserGroupDAO}.
     *
     * @return UserGroupDAO a new instance of {@link UserGroupDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getUserGroupDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('USER_GROUP_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'UserGroupDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/UserGroupDAO/' . $className . ".php");
    return new $className;
  }

    /** Belongs DAO creator
     *
     * This function returns a new instance of {@link BelongsDAO}.
     *
     * @return BelongsDAO a new instance of {@link BelongsDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getBelongsDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('BELONGS_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'BelongsDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/' . $className . ".php");
    return new $className;
  }

    /** Area DAO creator
     *
     * This function returns a new instance of {@link AreaDAO}.
     *
     * @return AreaDAO a new instance of {@link AreaDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getAreaDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('AREA_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'AreaDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/' . $className . ".php");
    return new $className;
  }

    /** Area History DAO creator
     *
     * This function returns a new instance of {@link AreaHistoryDAO}.
     *
     * @return AreaHistoryDAO a new instance of {@link AreaHistoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getAreaHistoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('AREA_HISTORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'AreaHistoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/AreaHistoryDAO/' . $className . ".php");
    return new $className;
  }

    /** City DAO creator
     *
     * This function returns a new instance of {@link CityDAO}.
     *
     * @return CityDAO a new instance of {@link CityDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getCityDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('CITY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'CityDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/CityDAO/' . $className . ".php");
    return new $className;
  }

    /** City History DAO creator
     *
     * This function returns a new instance of {@link CityHistoryDAO}.
     *
     * @return CityHistoryDAO a new instance of {@link CityHistoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getCityHistoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('CITY_HISTORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'CityHistoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/CityHistoryDAO/' . $className . ".php");
    return new $className;
  }

    /** Common Event DAO creator
     *
     * This function returns a new instance of {@link CommonEventDAO}.
     *
     * @return CommonEventDAO a new instance of {@link CommonEventDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getCommonEventDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('COMMON_EVENT_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'CommonEventDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/CommonEventDAO/' . $className . ".php");
    return new $className;
  }

    /** Customer DAO creator
     *
     * This function returns a new instance of {@link CustomerDAO}.
     *
     * @return CustomerDAO a new instance of {@link CustomerDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getCustomerDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('CUSTOMER_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'CustomerDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/' . $className . ".php");
    return new $className;
  }

    /** Custom Event DAO creator
     *
     * This function returns a new instance of {@link CustomEventDAO}.
     *
     * @return CustomEventDAO a new instance of {@link CustomEventDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getCustomEventDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('CUSTOM_EVENT_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'CustomEventDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/CustomEventDAO/' . $className . ".php");
    return new $className;
  }

    /** Extra Hour DAO creator
     *
     * This function returns a new instance of {@link ExtraHourDAO}.
     *
     * @return ExtraHourDAO a new instance of {@link ExtraHourDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getExtraHourDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('EXTRA_HOUR_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ExtraHourDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ExtraHourDAO/' . $className . ".php");
    return new $className;
  }

    /** Hour Cost History DAO creator
     *
     * This function returns a new instance of {@link HourCostHistoryDAO}.
     *
     * @return HourCostHistoryDAO a new instance of {@link HourCostHistoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getHourCostHistoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('HOUR_COST_HISTORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'HourCostHistoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/HourCostHistoryDAO/' . $className . ".php");
    return new $className;
  }

    /** Journey History DAO creator
     *
     * This function returns a new instance of {@link JourneyHistoryDAO}.
     *
     * @return JourneyHistoryDAO a new instance of {@link JourneyHistoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getJourneyHistoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('JOURNEY_HISTORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'JourneyHistoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/JourneyHistoryDAO/' . $className . ".php");
    return new $className;
  }

    /** Project DAO creator
     *
     * This function returns a new instance of {@link ProjectDAO}.
     *
     * @return ProjectDAO a new instance of {@link ProjectDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getProjectDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('PROJECT_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ProjectDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/' . $className . ".php");
    return new $className;
  }

    /** Project Schedule DAO creator
     *
     * This function returns a new instance of {@link ProjectScheduleDAO}.
     *
     * @return ProjectScheduleDAO a new instance of {@link ProjectScheduleDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getProjectScheduleDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('PROJECT_SCHEDULE_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ProjectScheduleDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ProjectScheduleDAO/' . $className . ".php");
    return new $className;
  }

    /** Project User DAO creator
     *
     * This function returns a new instance of {@link ProjectUserDAO}.
     *
     * @return ProjectUserDAO a new instance of {@link ProjectUserDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getProjectUserDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('PROJECT_USER_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ProjectUserDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ProjectUserDAO/' . $className . ".php");
    return new $className;
  }

    /** Requests DAO creator
     *
     * This function returns a new instance of {@link RequestsDAO}.
     *
     * @return RequestsDAO a new instance of {@link RequestsDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getRequestsDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('REQUESTS_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'RequestsDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/RequestsDAO/' . $className . ".php");
    return new $className;
  }

    /** Task DAO creator
     *
     * This function returns a new instance of {@link TaskDAO}.
     *
     * @return TaskDAO a new instance of {@link TaskDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getTaskDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('TASK_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'TaskDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/' . $className . ".php");
    return new $className;
  }

    /** Works DAO creator
     *
     * This function returns a new instance of {@link WorksDAO}.
     *
     * @return WorksDAO a new instance of {@link WorksDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getWorksDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('WORKS_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'WorksDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/WorksDAO/' . $className . ".php");
    return new $className;
  }

    /** Iteration DAO creator
     *
     * This function returns a new instance of {@link IterationDAO}.
     *
     * @return IterationDAO a new instance of {@link IterationDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getIterationDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('ITERATION_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'IterationDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/IterationDAO/' . $className . ".php");
    return new $className;
  }

    /** Story DAO creator
     *
     * This function returns a new instance of {@link StoryDAO}.
     *
     * @return StoryDAO a new instance of {@link StoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getStoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('STORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'StoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/StoryDAO/' . $className . ".php");
    return new $className;
  }

    /** Task Story DAO creator
     *
     * This function returns a new instance of {@link TaskStoryDAO}.
     *
     * @return TaskStoryDAO a new instance of {@link TaskStoryDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getTaskStoryDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('TASK_STORY_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'TaskStoryDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/TaskStoryDAO/' . $className . ".php");
    return new $className;
  }

    /** Module DAO creator
     *
     * This function returns a new instance of {@link ModuleDAO}.
     *
     * @return ModuleDAO a new instance of {@link ModuleDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getModuleDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('MODULE_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ModuleDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ModuleDAO/' . $className . ".php");
    return new $className;
  }

    /** Section DAO creator
     *
     * This function returns a new instance of {@link SectionDAO}.
     *
     * @return SectionDAO a new instance of {@link SectionDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getSectionDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('SECTION_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'SectionDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/SectionDAO/' . $className . ".php");
    return new $className;
  }

    /** Task Section DAO creator
     *
     * This function returns a new instance of {@link TaskSectionDAO}.
     *
     * @return TaskSectionDAO a new instance of {@link TaskSectionDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getTaskSectionDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('TASK_SECTION_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'TaskSectionDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/TaskSectionDAO/' . $className . ".php");
    return new $className;
  }

    /** Config DAO creator
     *
     * This function returns a new instance of {@link ConfigDAO}.
     *
     * @return ConfigDAO a new instance of {@link ConfigDAO}.
     * @throws {@link UnknownParameterException}
     */
  public static function getConfigDAO() {
    try {
      $className = ConfigurationParametersManager::getParameter('CONFIG_DAO');
    }
    catch(UnknownParameterException $e) {
      $backend = ConfigurationParametersManager::getParameter('DAO_BACKEND');
      $className = $backend . 'ConfigDAO';
    }

    include_once(PHPREPORT_ROOT . '/model/dao/ConfigDAO/' . $className . ".php");
    return new $className;
  }

}


/*// Test code
var_dump(DAOFactory::getUserDAO());
var_dump(DAOFactory::getUserGroupDAO());
var_dump(DAOFactory::getBelongsDAO());
var_dump(DAOFactory::getAreaDAO());
var_dump(DAOFactory::getAreaHistoryDAO());
var_dump(DAOFactory::getWorksDAO());
var_dump(DAOFactory::getRequestsDAO());
var_dump(DAOFactory::getCustomerDAO());
var_dump(DAOFactory::getTaskDAO());
var_dump(DAOFactory::getProjectDAO());
var_dump(DAOFactory::getSectorDAO());
var_dump(DAOFactory::getJourneyHistoryDAO());
var_dump(DAOFactory::getCityHistoryDAO());
var_dump(DAOFactory::getHourCostHistoryDAO());
var_dump(DAOFactory::getExtraHourDAO());
var_dump(DAOFactory::getCityDAO());
var_dump(DAOFactory::getCommonEventDAO());
var_dump(DAOFactory::getCustomEventDAO());
var_dump(DAOFactory::getProjectUserDAO());
var_dump(DAOFactory::getProjectScheduleDAO());
var_dump(DAOFactory::getIterationDAO());
var_dump(DAOFactory::getStoryDAO());
var_dump(DAOFactory::getTaskStoryDAO());
var_dump(DAOFactory::getModuleDAO());
var_dump(DAOFactory::getSectionDAO());
var_dump(DAOFactory::getTaskSectionDAO());*/
