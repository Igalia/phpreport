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

}
