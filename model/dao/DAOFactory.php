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

class DAOFactory
{

  /** Sector DAO creator
   *
   * This function returns a new instance of {@link SectorDAO}.
   *
   * @return SectorDAO a new instance of {@link SectorDAO}.
   */
  public static function getSectorDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/SectorDAO/PostgreSQLSectorDAO.php');
    return new PostgreSQLSectorDAO;
  }

  /** User DAO creator
   *
   * This function returns a new instance of {@link UserDAO}.
   *
   * @return UserDAO a new instance of {@link UserDAO}.
   */
  public static function getUserDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/UserDAO/PostgreSQLUserDAO.php');
    return new PostgreSQLUserDAO;
  }

  /** User Group DAO creator
   *
   * This function returns a new instance of {@link UserGroupDAO}.
   *
   * @return UserGroupDAO a new instance of {@link UserGroupDAO}.
   */
  public static function getUserGroupDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/UserGroupDAO/PostgreSQLUserGroupDAO.php');
    return new PostgreSQLUserGroupDAO;
  }

  /** Belongs DAO creator
   *
   * This function returns a new instance of {@link BelongsDAO}.
   *
   * @return BelongsDAO a new instance of {@link BelongsDAO}.
   */
  public static function getBelongsDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/BelongsDAO/PostgreSQLBelongsDAO.php');
    return new PostgreSQLBelongsDAO;
  }

  /** Area DAO creator
   *
   * This function returns a new instance of {@link AreaDAO}.
   *
   * @return AreaDAO a new instance of {@link AreaDAO}.
   */
  public static function getAreaDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/PostgreSQLAreaDAO.php');
    return new PostgreSQLAreaDAO;
  }

  /** Area History DAO creator
   *
   * This function returns a new instance of {@link AreaHistoryDAO}.
   *
   * @return AreaHistoryDAO a new instance of {@link AreaHistoryDAO}.
   */
  public static function getAreaHistoryDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/AreaHistoryDAO/PostgreSQLAreaHistoryDAO.php');
    return new PostgreSQLAreaHistoryDAO;
  }

  /**
   * @return UserGoalDAO
   */
  public static function getUserGoalDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/UserGoalDAO/PostgreSQLUserGoalDAO.php');
    return new PostgreSQLUserGoalDAO;
  }

  /** City DAO creator
   *
   * This function returns a new instance of {@link CityDAO}.
   *
   * @return CityDAO a new instance of {@link CityDAO}.
   */
  public static function getCityDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/CityDAO/PostgreSQLCityDAO.php');
    return new PostgreSQLCityDAO;
  }

  /** City History DAO creator
   *
   * This function returns a new instance of {@link CityHistoryDAO}.
   *
   * @return CityHistoryDAO a new instance of {@link CityHistoryDAO}.
   */
  public static function getCityHistoryDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/CityHistoryDAO/PostgreSQLCityHistoryDAO.php');
    return new PostgreSQLCityHistoryDAO;
  }

  /** Common Event DAO creator
   *
   * This function returns a new instance of {@link CommonEventDAO}.
   *
   * @return CommonEventDAO a new instance of {@link CommonEventDAO}.
   */
  public static function getCommonEventDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/CommonEventDAO/PostgreSQLCommonEventDAO.php');
    return new PostgreSQLCommonEventDAO;
  }

  /** Customer DAO creator
   *
   * This function returns a new instance of {@link CustomerDAO}.
   *
   * @return CustomerDAO a new instance of {@link CustomerDAO}.
   */
  public static function getCustomerDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/CustomerDAO/PostgreSQLCustomerDAO.php');
    return new PostgreSQLCustomerDAO;
  }

  /** Extra Hour DAO creator
   *
   * This function returns a new instance of {@link ExtraHourDAO}.
   *
   * @return ExtraHourDAO a new instance of {@link ExtraHourDAO}.
   */
  public static function getExtraHourDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/ExtraHourDAO/PostgreSQLExtraHourDAO.php');
    return new PostgreSQLExtraHourDAO;
  }

  /** Hour Cost History DAO creator
   *
   * This function returns a new instance of {@link HourCostHistoryDAO}.
   *
   * @return HourCostHistoryDAO a new instance of {@link HourCostHistoryDAO}.
   */
  public static function getHourCostHistoryDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/HourCostHistoryDAO/PostgreSQLHourCostHistoryDAO.php');
    return new PostgreSQLHourCostHistoryDAO;
  }

  /** Journey History DAO creator
   *
   * This function returns a new instance of {@link JourneyHistoryDAO}.
   *
   * @return JourneyHistoryDAO a new instance of {@link JourneyHistoryDAO}.
   */
  public static function getJourneyHistoryDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/JourneyHistoryDAO/PostgreSQLJourneyHistoryDAO.php');
    return new PostgreSQLJourneyHistoryDAO;
  }

  /** Project DAO creator
   *
   * This function returns a new instance of {@link ProjectDAO}.
   *
   * @return ProjectDAO a new instance of {@link ProjectDAO}.
   */
  public static function getProjectDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
    return new PostgreSQLProjectDAO;
  }

  /** Project User DAO creator
   *
   * This function returns a new instance of {@link ProjectUserDAO}.
   *
   * @return ProjectUserDAO a new instance of {@link ProjectUserDAO}.
   */
  public static function getProjectUserDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/ProjectUserDAO/PostgreSQLProjectUserDAO.php');
    return new PostgreSQLProjectUserDAO;
  }

  /** Task DAO creator
   *
   * This function returns a new instance of {@link TaskDAO}.
   *
   * @return TaskDAO a new instance of {@link TaskDAO}.
   */
  public static function getTaskDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/PostgreSQLTaskDAO.php');
    return new PostgreSQLTaskDAO;
  }

  /** Template DAO creator
   *
   * This function returns a new instance of {@link TemplateDAO}.
   *
   * @return TemplateDAO a new instance of {@link TemplateDAO}.
   */
  public static function getTemplateDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/TemplateDAO/PostgreSQLTemplateDAO.php');
    return new PostgreSQLTemplateDAO;
  }

  /** Config DAO creator
   *
   * This function returns a new instance of {@link ConfigDAO}.
   *
   * @return ConfigDAO a new instance of {@link ConfigDAO}.
   */
  public static function getConfigDAO()
  {
    include_once(PHPREPORT_ROOT . '/model/dao/ConfigDAO/PostgreSQLConfigDAO.php');
    return new PostgreSQLConfigDAO;
  }
}
