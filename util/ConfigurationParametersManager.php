<?php

/** File for ConfigurationParametersManager
 *
 *  This file just contains {@link ConfigurationParametersManager}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage util
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once('phpreport/util/UnknownParameterException.php');
require_once('phpreport/config/config.php');

/** Configuration parameters manager
 *
 *  This class is used for obtaining configuration parameters values from the file {@link config.php}.
 *
 * @see config.php
 */
class ConfigurationParametersManager {


    /** Parameters values retriever.
     *
     * This function retrieves the value of the parameter with the name <var>$parameterName</var>.
     *
     * @param string $parameterName the name of the parameter we want to retrieve.
     * @return string the value of the parameter.
     * @throws {@link UnknownParameterException}
     */
  public static function getParameter($parameterName) {

    if (defined($parameterName))
      return constant($parameterName);

    throw new UnknownParameterException($parameterName);
  }

}


/*// Test code
echo ConfigurationParametersManager::getParameter('DB_PORT');
echo "\n";
echo ConfigurationParametersManager::getParameter('USER_DAO');
echo "\n";
echo ConfigurationParametersManager::getParameter('TASK_DAO');
echo "\n";
echo ConfigurationParametersManager::getParameter('unknownParameter');
echo "\n";
*/
