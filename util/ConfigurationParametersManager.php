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


/** File for ConfigurationParametersManager
 *
 *  This file just contains {@link ConfigurationParametersManager}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage util
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */
require_once(PHPREPORT_ROOT . '/vendor/autoload.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
/** Configuration parameters manager
 *
 *  This class is used for obtaining configuration parameters values from the file {@link config.php}.
 *
 * @see config.php
 */
class ConfigurationParametersManager
{
  /** Parameters values retriever.
   *
   * This function retrieves the value of the parameter with the name <var>$parameterName</var>.
   *
   * @param string $parameterName the name of the parameter we want to retrieve.
   * @return string the value of the parameter.
   * @throws {@link UnknownParameterException}
   */
  public static function getParameter($parameterName)
  {
    if (!defined('ENV_LOADED')) {
      $dotenv = Dotenv\Dotenv::createMutable(PHPREPORT_ROOT);
      $dotenv->load();
      define('ENV_LOADED', true);
    }

    $parameterValue = $_SERVER[$parameterName] ?? NULL;

    if (!is_null($parameterValue) and $parameterValue !== false) {
      return trim($parameterValue, '"');
    }

    throw new UnknownParameterException($parameterName);
  }
}
