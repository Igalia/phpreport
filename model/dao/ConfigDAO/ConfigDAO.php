<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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


/** File for {@link ConfigDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Config
 *
 *  Base class for any implementations of the DAO corresponding to the
 *  application configuration table (config), providing a common interface.
 *
 * @see DAOFactory::getConfigDAO()
 */
abstract class ConfigDAO extends BaseDAO {

    /** Config DAO constructor.
     *
     * Default constructor of ConfigDAO, it just calls parent constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
        parent::__construct();
    }

    /** Get version number.
     *
     * Get database version number.
     *
     * @return String a string containing the version number
     */
    public abstract function getVersionNumber();

    /** Query PhpReport task block configuration.
     *
     * Check if PhpReport configuration allows writing tasks on the specified
     * date.
     *
     * @return boolean returns wether tasks for the speficied date can be
     *         written or not.
     */
    public abstract function isWriteAllowedForDate(DateTime $date);

    /** Get PhpReport task block configuration.
     *
     * Return all the values implicated in the configuration of task block by
     * date.
     *
     * @return array "enabled" returns wether task block is enabled or not.
     *         "numberOfDays" returns the number of days configured as time
     *         limit.
     */
    public abstract function getTaskBlockConfiguration();

    /** Store PhpReport task block configuration.
     *
     * Change PhpReport configuration to allow or prevent writing tasks based on
     * the date of those tasks.
     *
     * @param boolean $enabled Enable of disable the task block feature.
     * @param int $numberOfDays Set the number of days in the past when tasks
     *        tasks cannot be altered.
     * @return boolean returns wether changes were saved or not.
     */
    public abstract function setTaskBlockConfiguration($enabled, $numberOfDays);

    /** User value object constructor for PostgreSQL.
     *
     * The method is supposed to create value objects from the rows retrieved
     * from database. In this particular DAO we don't use value objects, since
     * we are only interested in retrieving scalar values from the config table.
     * Therefore, this DAO isolates and returns that scalar.
     *
     * @param array $row an array with the User values from a row.
     * @return mixed a scalar value or null if the configuration parameter is
     *         not set.
     */
    protected function setValues($row)
    {
        if (isset($row[0])) {
            return $row[0];
        }
        return null;
    }

}
