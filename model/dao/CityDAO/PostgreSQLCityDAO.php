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
class PostgreSQLCityDAO extends CityDAO{

    /** City DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link CityDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see CityDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** City value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CityVO} with data retrieved from database.
     *
     * @param array $row an array with the City values from a row.
     * @return CityVO a {@link CityVO} with its properties set to the values from <var>$row</var>.
     * @see CityVO
     */
    protected function setValues($row)
    {

    $cityVO = new CityVO();

        $cityVO->setId($row[id]);
        $cityVO->setName($row[name]);

    return $cityVO;
    }

    /** Cities retriever by id.
     *
     * This function retrieves the row from City table with the id <var>$cityId</var> and creates a {@link CityVO} with its data.
     *
     * @param int $cityId the id of the row we want to retrieve.
     * @return CityVO a value object {@link CityVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($cityId) {
        if (!is_numeric($cityId))
        throw new SQLIncorrectTypeException($cityId);
        $sql = "SELECT * FROM city WHERE id=" . $cityId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** City Histories retriever by City id.
     *
     * This function retrieves the rows from CityHistory table that are assigned to the City with
     * the id <var>$cityId</var> and creates a {@link CityHistoryVO} with data from each row.
     *
     * @param int $cityId the id of the City whose City Histories we want to retrieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CityHistoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCityHistories($cityId) {

    $dao = DAOFactory::getCityHistoryDAO();
    return $dao->getByCityId($cityId);

    }

    /** Common Events retriever by City id.
     *
     * This function retrieves the rows from Common Event table that are assigned to the City with
     * the id <var>$cityId</var> and creates a {@link CommonEventVO} with data from each row.
     *
     * @param int $cityId the id of the City whose Common Events we want to retrieve.
     * @return array an array with value objects {@link CommonEventVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see CommonEventDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getCommonEvents($cityId) {

    $dao = DAOFactory::getCommonEventDAO();
    return $dao->getByCityId($cityId);

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
        $sql = "SELECT * FROM city ORDER BY id ASC";
        return $this->execute($sql);
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

        if($cityVO->getId() >= 0) {
            $currcityVO = $this->getById($cityVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currcityVO) > 0) {

            $sql = "UPDATE city SET name=" . DBPostgres::checkStringNull($cityVO->getName()) . " WHERE id=".$cityVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_city_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
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

        $sql = "INSERT INTO city (name) VALUES (" . DBPostgres::checkStringNull($cityVO->getName()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_city_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $cityVO->setId(DBPostgres::getId($this->connect, "city_id_seq"));

        $affectedRows = pg_affected_rows($res);

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

        // Check for a city ID.
        if($cityVO->getId() >= 0) {
            $currcityVO = $this->getById($cityVO->getId());
        }

        // Delete a city.
        if(sizeof($currcityVO) > 0) {
            $sql = "DELETE FROM city WHERE id=".$cityVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLcityDAO();

// We create a new city

$city = new cityVO();

$city->setName("Shanghai");

$dao->create($city);

print ("New city Id is ". $city->getId() ."\n");

// We search for the new Id

$city = $dao->getById($city->getId());

print ("New city Id found is ". $city->getId() ."\n");

// We update the city with a differente name

$city->setName("Laos");

$dao->update($city);

// We search for the new name

$city = $dao->getById($city->getId());

print ("New city name found is ". $city->getName() ."\n");

// We delete the new city

$dao->delete($city);*/
