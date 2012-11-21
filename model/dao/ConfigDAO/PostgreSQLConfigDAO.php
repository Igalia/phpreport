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


/** File for {@link PostgreSQLConfigDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/dao/ConfigDAO/ConfigDAO.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');

/** DAO for Config in PostgreSQL
 *
 *  Implementation of ConfigDAO for a PostgreSQL database.
 *
 * @see DAOFactory::getUserDAO()
 */
class PostgreSQLConfigDAO extends ConfigDAO {

    /** Config DAO constructor.
     *
     * Default constructor of ConfigDAO, it just calls parent constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** Get version number.
     *
     * Get database version number.
     *
     * @return String a string containing the version number
     */
    public function getVersionNumber() {
        $sql = "SELECT version FROM config";

        $result = $this->execute($sql);

        if (!is_null($result[0])) {
            return $result[0];
        }
    }

    /** Query PhpReport task block configuration.
     *
     * Check if PhpReport configuration allows writing tasks on the specified
     * date.
     *
     * @return boolean returns wether tasks for the speficied date can be
     *         written or not.
     */
    public function isWriteAllowedForDate(DateTime $date){
        $sql = "SELECT block_tasks_by_time_enabled FROM config";
        $enabled = $this->execute($sql);
        $enabled = (strtolower($enabled[0]) == "t");

        $sql = "SELECT block_tasks_by_time_number_of_days FROM config";
        $days = $this->execute($sql);

        if(!$enabled || is_null($days[0]) || $days[0] == 0) {
            return true;
        }

        //times are reset to 0:00 because we don't need it
        $dateNotWritable = new DateTime();
        $dateNotWritable->setTime(0,0);
        $dateNotWritable->sub(new DateInterval('P'.$days[0].'D'));
        $date->setTime(0,0);

        return $date > $dateNotWritable;
    }

    /** Get PhpReport task block configuration.
     *
     * Return all the values implicated in the configuration of task block by
     * date.
     *
     * @return array "enabled" returns wether task block is enabled or not.
     *         "numberOfDays" returns the number of days configured as time
     *         limit.
     */
    public function getTaskBlockConfiguration() {
        $sql = "SELECT block_tasks_by_time_enabled FROM config";
        $enabled = $this->execute($sql);

        $sql = "SELECT block_tasks_by_time_number_of_days FROM config";
        $days = $this->execute($sql);

        return array(
            "enabled" => (strtolower($enabled[0]) == "t"),
            "numberOfDays" => $days[0]);
    }

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
    public function setTaskBlockConfiguration($enabled, $numberOfDays) {
        $sql = "UPDATE config SET " .
                "block_tasks_by_time_number_of_days =" .
                DBPostgres::checkNull($numberOfDays) . "," .
                "block_tasks_by_time_enabled = " .
                DBPostgres::boolToString($enabled);

        $res = pg_query($this->connect, $sql);

        return ($res != NULL);
    }

}
