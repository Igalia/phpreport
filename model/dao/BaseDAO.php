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


/** File for BaseDAO
 *
 *  This file just contains {@link BaseDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/util/DatabaseConnectionManager.php');
include_once(PHPREPORT_ROOT . '/util/DBConnectionErrorException.php');
include_once(PHPREPORT_ROOT . '/util/SQLQueryErrorException.php');

/** Base class for all simple DAOs
 *
 *  This class is extended by every DAO we create responsible for working with a single table matched to a value object, and it contains their most basic variables and functions.
 *
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class BaseDAO {

    /** The connection to DB.
     *
     * This variable contains the connection to the database, with the parameters read from <i>{@link config.php}</i>.
     *
     * @var resource
     * @see __construct()
     */
    protected $connect;

    /** The connection to DB.
     *
     * PDO object with an open connection to the database, initialized in the
     * class constructor.
     *
     * @var resource
     * @see __construct()
     */
    protected PDO $pdo;

    /** Base constructor.
     *
     * This is the base constructor of all simple DAOs, and it just creates the
     * connection with the parameters read from <i>{@link config.php}</i>,
     * storing it in <var>{@link $connect}</var>.
     * It also sets up a connection using the PDO API, via
     * DatabaseConnectionManager. Eventually, all DAOs should use it.
     *
     * @see ConfigurationParametersManager, config.php
     * @throws {@link ConnectionErrorException}
     * @todo create the connection pool and remove the connection and its creation from {@link BaseDAO}.
     */
    protected function __construct() {
        $parameters[] = ConfigurationParametersManager::getParameter('DB_HOST');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_PORT');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_USER');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_NAME');
        $parameters[] = ConfigurationParametersManager::getParameter('DB_PASSWORD');
        $parameters[] = ConfigurationParametersManager::getParameter('EXTRA_DB_CONNECTION_PARAMETERS');

        $connectionString = "host=$parameters[0] port=$parameters[1] user=$parameters[2] dbname=$parameters[3] password=$parameters[4] $parameters[5]";

        $this->connect = pg_connect($connectionString);
        if ($this->connect == NULL) throw new DBConnectionErrorException($connectionString);

        pg_set_error_verbosity($this->connect, PGSQL_ERRORS_VERBOSE);

        // PDO setup for the DAOs that need it.
        $this->pdo = DatabaseConnectionManager::getPDO();
    }

    /** Value object constructor.
     *
     * This is the function that DAOs will use to create new value objects with data retrieved from database.
     *
     * @param array $row an array with the values from a row.
     * @return mixed a value object with its properties set to the values from <var>$row</var>.
     */
    abstract protected function setValues($row);

    /** SQL retrieving data sentences performer.
     *
     * This function executes the retrieving data sentence in <var>$sql</var> and calls the function {@link setValues()} in order to
     * create a value object with each row, and returning them in an array afterwards. It uses the connection stored in <var>{@link $connect}</var>.
     *
     * @param string $sql a simple SQL 'select' sentence as a string.
     * @return array an associative array of value objects created with the data retrieved with <var>$sql</var>.
     * @throws {@link SQLQueryErrorException}
     * @see setValues()
     */
    protected function execute($sql) {
        $res = @pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $VO = array();

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++) {
                $row = @pg_fetch_array($res);
                $VO[$i] = $this->setValues($row);
            }
        }

        @pg_freeresult($res);

        return $VO;
    }

    /**
     * Encapsulates the logic to run a select query via the PDO API, that would
     * return an array of VO objects.
     * @param string $statement: An SQL select query with placeholders for
     * parameter binding according to PDO syntax (":parameter_name").
     * @param array $data: Data for parameter binding, array indexes must match
     * the placeholder names in the query.
     * @param string $fetchClass: The name of the class that should be created
     * for every result row.
     */
    protected function runSelectQuery(string $statement, array $data, string $fetchClass) {
        try {
            $statement = $this->pdo->prepare($statement);
            $statement->execute($data);
            return $statement->fetchAll(PDO::FETCH_CLASS, $fetchClass);
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }
    }

}
