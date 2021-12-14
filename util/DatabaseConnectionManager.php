<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

class DatabaseConnectionManager {

    /** The connection to DB.
     *
     * PDO object with an open connection to the database.
     *
     * @var resource
     * @see __construct()
     */
    private static PDO $pdo;

    /** Setup database connection via PDO.
     *
     * It sets up everything for database connection via the PDO API, using th
     * parameters read from config.php, and saves the open connection in the
     * static property $pdo.
     *
     * @throws {@link DBConnectionErrorException}
     */
    private static function setupPDOConnection() {
        // TODO: EXTRA_DB_CONNECTION_PARAMETERS used to expect pg_connect
        // parameters, which were space-separated, but PDO requires semicolons
        $connectionString = sprintf("pgsql:host=%s;port=%d;user=%s;dbname=%s;password=%s;%s",
            ConfigurationParametersManager::getParameter('DB_HOST'),
            ConfigurationParametersManager::getParameter('DB_PORT'),
            ConfigurationParametersManager::getParameter('DB_USER'),
            ConfigurationParametersManager::getParameter('DB_NAME'),
            ConfigurationParametersManager::getParameter('DB_PASSWORD'),
            ConfigurationParametersManager::getParameter('EXTRA_DB_CONNECTION_PARAMETERS'));

        try {
            self::$pdo = new PDO($connectionString);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log('Connection failed: ' . $e->getMessage());
            throw new DBConnectionErrorException($connectionString);
        }
    }

    /** Get or create a PDO object for database connection.
     *
     * Retrieve a PDO object for database connection. The first time this method
     * is called, it will create the object, and it will be reused in subsequent
     * calls.
     */
    public static function getPDO(): PDO {
        if (!isset(self::$pdo)) {
            self::setupPDOConnection();
        }
        return self::$pdo;
    }
}
