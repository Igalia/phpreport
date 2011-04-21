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


/** File for BaseRelationshipDAO
 *
 *  This file just contains {@link BaseDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/DBConnectionErrorException.php');
include_once(PHPREPORT_ROOT . '/util/SQLQueryErrorException.php');

/** Base class for all relationship DAOs
 *
 *  This class is extended by every DAO we create responsible for working with many tables due to a relationship,
 *  and it contains their most basic variables and functions. All relationships are binary ones, so we have two edges we'll call A and B.
 *
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class BaseRelationshipDAO {

     /** The connection to DB.
     *
     * This variable contains the connection to the database, with the parameters read from <i>{@link config.php}</i>.
     *
     * @var resource
     * @see __construct()
     */
    protected $connect;

    /** Base constructor.
     *
     * This is the base constructor of all relationship DAOs, and it just creates the connection with the parameters read from <i>{@link config.php}</i>, storing it in <var>{@link $connect}</var>.
     *
     * @see ConfigurationParametersManager, config.php
     * @throws {@link DBConnectionErrorException}
     * @todo create the connection pool and remove the connection and its creation from {@link BaseRelationshipDAO}.
     */
    function __construct() {

    $parameters[] = ConfigurationParametersManager::getParameter('DB_HOST');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_PORT');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_USER');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_NAME');
    $parameters[] = ConfigurationParametersManager::getParameter('DB_PASSWORD');

    $connectionString = "host=$parameters[0] port=$parameters[1] user=$parameters[2] dbname=$parameters[3] password=$parameters[4]";

    $this->connect = pg_connect($connectionString);
     if ($this->connect == NULL) throw new DBConnectionErrorException($connectionString);

    pg_set_error_verbosity($this->connect, PGSQL_ERRORS_VERBOSE);


    }

    /** Value object constructor from edge A.
     *
     * This is the function that DAOs will use to create new value objects from edge A with data retrieved from database.
     *
     * @param array $row an array with the values from a row.
     * @return mixed a value object with its properties set to the values from <var>$row</var>.
     */
    abstract protected function setAValues($row);

    /** Value object constructor from edge B.
     *
     * This is the function that DAOs will use to create new value objects from edge B with data retrieved from database.
     *
     * @param array $row an array with the values from a row.
     * @return mixed a value object with its properties set to the values from <var>$row</var>.
     */
    abstract protected function setBValues($row);

    /** SQL retrieving data sentences performer.
     *
     * This function executes the retrieving data sentence in <var>$sql</var> and just returns the results as arrays.
     * It's been created just for checking rows existence in the relationship table.
     * It uses the connection stored in <var>{@link $connect}</var>.
     *
     * @param string $sql a simple SQL 'select' sentence as a string.
     * @return array an associative array of data retrieved with <var>$sql</var>.
     * @throws {@link SQLQueryErrorException}
     */
    protected function execute($sql) {
        $res = @pg_query($this->connect, $sql);
        $VO = array();
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++) {
                $row = @pg_fetch_array($res);
                $VO[$i] = $row;
            }
        }

    @pg_freeresult($res);

        return $VO;
    }

     /** SQL retrieving data sentences performer from edge A.
     *
     * This function executes the retrieving data sentence in <var>$sql</var> and calls the function {@link setBValues()}
     * (it is called from edge A, so we read objects of B) in order to create a value object with each row, and returning
     * them in an array afterwards. It uses the connection stored in <var>{@link $connect}</var>.
     *
     * @param string $sql a simple SQL 'select' sentence as a string.
     * @return array an associative array of value objects from B created with the data retrieved with <var>$sql</var>.
     * @throws {@link SQLQueryErrorException}
     * @see setBValues(), executeFromB()
     */
    protected function executeFromA($sql) {
        $res = @pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++) {
                $row = @pg_fetch_array($res);
                $VO[$i] = $this->setBValues($row);
            }
        }
        return $VO;
    }

     /** SQL retrieving data sentences performer from edge B.
     *
     * This function executes the retrieving data sentence in <var>$sql</var> and calls the function {@link setAValues()}
     * (it is called from edge B, so we read objects of A) in order to create a value object with each row, and returning
     * them in an array afterwards. It uses the connection stored in <var>{@link $connect}</var>.
     *
     * @param string $sql a simple SQL 'select' sentence as a string.
     * @return array an associative array of value objects from A created with the data retrieved with <var>$sql</var>.
     * @throws {@link SQLQueryErrorException}
     * @see setAValues(), executeFromA()
     */
    protected function executeFromB($sql) {
        $res = @pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++) {
                $row = @pg_fetch_array($res);
                $VO[$i] = $this->setAValues($row);
            }
        }
        return $VO;
    }
}
