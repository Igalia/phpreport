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

    /** Query PhpReport task block configuration.
     *
     * Check if PhpReport configuration allows writing tasks on the specified
     * date.
     *
     * @return boolean returns wether tasks for the speficied date can be
     *         written or not.
     */
    public function isWriteAllowedForDate(DateTime $date){
        $config = $this->getTaskBlockConfiguration();

        $dayLimitDate = NULL;
        $dateBlockDate = NULL;
        if($config["dayLimitEnabled"] && !is_null($config["numberOfDays"]) && $config["numberOfDays"] > 0) {
            // Limit by number of dates is enabled
            $dayLimitDate = new DateTime();
            $dayLimitDate->setTime(0,0);
            $dayLimitDate->sub(new DateInterval('P'.$config["numberOfDays"].'D'));
        }
        if($config["dateLimitEnabled"] && !is_null($config["date"])) {
            // Limit by date is enabled
            $dateBlockDate = $config["date"];
        }

        //times are reset to 0:00 because we don't need it
        $date->setTime(0,0);

        // Any date is bigger than NULL, that's why this works in case some of
        // the limits are disabled and their dates are NULL.
        return $date > max($dayLimitDate, $dateBlockDate);
    }

    /** Get PhpReport task block configuration.
     *
     * Return all the values implicated in the configuration of task block by
     * date.
     *
     * @return array "dayLimitEnabled" returns whether task block by day limit is
     *         enabled or not.
     *         "numberOfDays" returns the number of days configured as day
     *         limit. May be null.
     *         "dateLimitEnabled" returns whether task block by date is enabled
     *         or not.
     *         "date" returns the date before which tasks may not be edited. May
     *         be null.
     */
    public function getTaskBlockConfiguration() {
        $sql = "SELECT block_tasks_by_day_limit_enabled,".
                   "block_tasks_by_day_limit_number_of_days,".
                   "block_tasks_by_date_enabled,".
                   "block_tasks_by_date_date ".
                   "FROM config";
        $config = array();

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $config = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }
        return array(
            "dayLimitEnabled" => $config["block_tasks_by_day_limit_enabled"],
            "dateLimitEnabled" => $config["block_tasks_by_date_enabled"],
            "numberOfDays" => $config["block_tasks_by_day_limit_number_of_days"],
            "date" => is_null($config["block_tasks_by_date_date"])? NULL : date_create($config["block_tasks_by_date_date"]));
    }

    /** Store PhpReport task block configuration.
     *
     * Change PhpReport configuration to allow or prevent writing tasks based on
     * the date of those tasks.
     *
     * @param boolean $dayLimitEnabled Enable of disable a day limit for tasks,
     *        so tasks older than a certain number of days would be blocked.
     * @param int $numberOfDays Set the number of days in the past when tasks
     *        tasks cannot be altered.
     * @param boolean $dateLimitEnabled Enable of disable a limit date for tasks,
     *        so tasks before that date would be blocked.
     * @param DateTime $date Tasks before this date would be blocked if
     *        $dateLimitEnabled is set.
     * @return boolean returns wether changes were saved or not.
     */
    public function setTaskBlockConfiguration($dayLimitEnabled, $numberOfDays,
            $dateLimitEnabled, $date) {
        $affectedRows = 0;

        $sql = "UPDATE config SET " .
                "block_tasks_by_day_limit_enabled = :dayLimitEnabled, " .
                "block_tasks_by_day_limit_number_of_days = :numberOfDays, " .
                "block_tasks_by_date_enabled = :dateLimitEnabled, " .
                "block_tasks_by_date_date = :date";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":dayLimitEnabled", $dayLimitEnabled, PDO::PARAM_BOOL);
            $statement->bindValue(":numberOfDays", $numberOfDays, PDO::PARAM_INT);
            $statement->bindValue(":dateLimitEnabled", $dateLimitEnabled, PDO::PARAM_BOOL);
            $statement->bindValue(":date", DBPostgres::formatDate($date), PDO::PARAM_STR);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }
        return $affectedRows != 0;
    }

}
