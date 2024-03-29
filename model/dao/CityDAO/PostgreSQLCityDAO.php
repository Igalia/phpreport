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


/** File for PostgreSQLCityDAO
 *
 *  This file just contains {@link PostgreSQLCityDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CityDAO/CityDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CommonEventDAO/PostgreSQLCommonEventDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CityHistoryDAO/PostgreSQLCityHistoryDAO.php');

/** DAO for Citys in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link CityDAO}.
 *
 * @see CityDAO, CityVO
 */
class PostgreSQLCityDAO extends CityDAO {

    /** City DAO for PostgreSQL constructor.
     *
     * This constructor just calls its parent's constructor. It's necessary
     * to overwrite the visibility of the BaseDAO constructor, which is set
     * to `protected`.
     *
     * @throws {@link DBConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** City retriever.
     *
     * This function retrieves all rows from City table and creates a {@link CityVO} with data from each row.
     *
     * @return array an array with value objects {@link CityVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        return $this->runSelectQuery(
            "SELECT * FROM city ORDER BY id ASC", [], 'CityVO');
    }

    /** City updater.
     *
     * This function updates the data of a City by its {@link CityVO}.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(CityVO $cityVO) {
        $affectedRows = 0;

        $sql = "UPDATE city SET name=:name WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":name", $cityVO->getName(), PDO::PARAM_STR);
            $statement->bindValue(":id", $cityVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            if (strpos($e->getMessage(), "unique_city_name"))
                throw new SQLUniqueViolationException($e->getMessage());
            else throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;
    }

    /** City creator.
     *
     * This function creates a new row for a City by its {@link CityVO}. The internal id of <var>$cityVO</var> will be set after its creation.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(CityVO $cityVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO city (name) VALUES (:name)";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":name", $cityVO->getName(), PDO::PARAM_STR);
            $statement->execute();

            $cityVO->setId($this->pdo->lastInsertId('city_id_seq'));

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            if (strpos($e->getMessage(), "unique_city_name"))
                throw new SQLUniqueViolationException($e->getMessage());
            else throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;
    }

    /** City deleter.
     *
     * This function deletes the data of a City by its {@link CityVO}.
     *
     * @param CityVO $cityVO the {@link CityVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(CityVO $cityVO) {
        $affectedRows = 0;

        $sql = "DELETE FROM city WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":id", $cityVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $affectedRows = $statement->rowCount();
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage());
            throw new SQLQueryErrorException($e->getMessage());
        }

        return $affectedRows;
    }
}
