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


/** File for PostgreSQLCityHistoryDAO
 *
 *  This file just contains {@link PostgreSQLCityHistoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/CityHistoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/CityHistoryDAO/CityHistoryDAO.php');

/** DAO for City Histories in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link CityHistoryDAO}.
 *
 * @see CityHistoryDAO, CityHistoryVO
 */
class PostgreSQLCityHistoryDAO extends CityHistoryDAO{

    /** City History DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link CityHistoryDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see CityHistoryDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** City History value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CityHistoryVO} with data retrieved from database.
     *
     * @param array $row an array with the City History values from a row.
     * @return CityHistoryVO a {@link CityHistoryVO} with its properties set to the values from <var>$row</var>.
     * @see CityHistoryVO
     */
    protected function setValues($row)
    {

    $cityHistoryVO = new CityHistoryVO();

        $cityHistoryVO->setId($row['id']);
        $cityHistoryVO->setInitDate(date_create($row['init_date']));
    $cityHistoryVO->setUserId($row['usrid']);
    if (is_null($row['end_date']))
            $cityHistoryVO->setEndDate(NULL);
    else
        $cityHistoryVO->setEndDate(date_create($row['end_date']));
    $cityHistoryVO->setCityId($row['cityid']);

    return $cityHistoryVO;
    }

    /** City History retriever by id for PostgreSQL.
     *
     * This function retrieves the row from City History table with the id <var>$cityHistoryId</var> and creates a {@link CityHistoryVO} with its data.
     *
     * @param int $cityHistoryId the id of the row we want to retrieve.
     * @return CityHistoryVO a value object {@link CityHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($cityHistoryId) {
    if (!is_numeric($cityHistoryId))
        throw new SQLIncorrectTypeException($cityHistoryId);
        $sql = "SELECT * FROM city_history WHERE id=" . $cityHistoryId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** City History retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from City History table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link CityHistoryVO} with data from each row.
     *
     * @param int $userId the id of the User whose City History we want to retrieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM city_history WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** City History current entry retriever by User id for PostgreSQL.
     *
     * This function retrieves the current row from City History table that is associated with the User with
     * the id <var>$userId</var> and creates a {@link CityHistoryVO} with data from that row.
     *
     * @param int $userId the id of the User whose City History current entry we want to retrieve.
     * @return CityHistoryVO a value object {@link CityHistoryVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getCurrentByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM city_history WHERE usrid=" . $userId . " AND end_date IS NULL";
    $result = $this->execute($sql);
    return $result[0];
    }

    /** City History retriever by User id and date interval for PostgreSQL.
     *
     * This function retrieves the rows from City History table that are associated with the User with
     * the id <var>$userId</var> and that full lay inside the interval defined by <var>$init</var> and <var>$end</var>
     * and creates a {@link CityHistoryVO} with data from each row.
     * If we don't indicate a User id, then entries for all users are returned.
     *
     * @param DateTime $init the DateTime object that represents the beginning of the date interval.
     * @param DateTime $end the DateTime object that represents the end of the date interval (included).
     * @param int $userId the id of the User whose City History we want to retrieve. It's optional.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by the User database internal identifier and the beginning date.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByIntervals(DateTime $init, DateTime $end, $userId = NULL) {
    if (is_null($userId))
        $sql = "SELECT * FROM city_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL))
        ORDER BY usrid ASC, init_date ASC";
    elseif (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        else
        $sql = "SELECT * FROM city_history
        WHERE ((init_date <= " . DBPostgres::formatDate($end) . " OR init_date IS NULL) AND (end_date >= " . DBPostgres::formatDate($init) . " OR end_date IS NULL) AND (usrId = $userId))
        ORDER BY usrid ASC, init_date ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** City Histories retriever by City id for PostgreSQL.
     *
     * This function retrieves the rows from City History table that are associated with the City with
     * the id <var>$cityId</var> and creates a {@link CityHistoryVO} with data from each row.
     *
     * @param int $cityId the id of the City whose City History associated entries we want to retrieve.
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByCityId($cityId) {
        $sql = "SELECT * FROM city_history WHERE cityid=" . $cityId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** City Histories retriever for PostgreSQL.
     *
     * This function retrieves all rows from City History table and creates a {@link CityHistoryVO} with data from each row.
     *
     * @return array an array with value objects {@link CityHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM city_history ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** City History updater for PostgreSQL.
     *
     * This function updates the data of a City History by its {@link CityHistoryVO}.
     *
     * @param CityHistoryVO $cityHistoryVO the {@link CityHistoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(CityHistoryVO $cityHistoryVO) {

        $affectedRows = 0;

        if($cityHistoryVO->getId() >= 0) {
            $currCityHistoryVO = $this->getById($cityHistoryVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currCityHistoryVO) > 0) {

        $sql = "UPDATE city_history SET init_date=" . DBPostgres::formatDate($cityHistoryVO->getInitDate()) . ", usrid=" . DBPostgres::checkNull($cityHistoryVO->getUserId()) . ", cityid=" . DBPostgres::checkNull($cityHistoryVO->getCityId()) . ", end_date=" . DBPostgres::formatDate($cityHistoryVO->getEndDate()) . " WHERE id=".$cityHistoryVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_city_history_user_date"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;

    }

    /** City History creator for PostgreSQL.
     *
     * This function creates a new row for a City History by its {@link CityHistoryVO}. The internal id of <var>$cityHistoryVO</var> will be set after its creation.
     *
     * @param CityHistoryVO $cityHistoryVO the {@link CityHistoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(CityHistoryVO $cityHistoryVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO city_history (init_date, usrid, cityid, end_date) VALUES (" . DBPostgres::formatDate($cityHistoryVO->getInitDate()) . ", " . DBPostgres::checkNull($cityHistoryVO->getUserId()) . ", " . DBPostgres::checkNull($cityHistoryVO->getCityId()) . ", " . DBPostgres::formatDate($cityHistoryVO->getEndDate()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_city_history_user_date"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $cityHistoryVO->setId(DBPostgres::getId($this->connect, "city_history_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** City History deleter for PostgreSQL.
     *
     * This function deletes the data of a City History by its {@link CityHistoryVO}.
     *
     * @param CityHistoryVO $cityHistoryVO the {@link CityHistoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(CityHistoryVO $cityHistoryVO) {
        $affectedRows = 0;

        // Check for a city history entry ID.
        if($cityHistoryVO->getId() >= 0) {
            $currCityHistoryVO = $this->getById($cityHistoryVO->getId());
        }

        // If it exists, then delete.
        if(sizeof($currCityHistoryVO) > 0) {
            $sql = "DELETE FROM city_history WHERE id=".$cityHistoryVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLcityHistoryDAO();

// We create a new city history entry

$cityHistory = new cityHistoryVO();

$cityHistory->setInitDate(date_create('2005-12-21'));
$cityHistory->setUserId(1);
$cityHistory->setCityId(1);

$dao->create($cityHistory);

print ("New city history entry Id is ". $cityHistory->getId() ."\n");

// We search for the new Id

$cityHistory = $dao->getById($cityHistory->getId());

print ("New city history entry Id found is ". $cityHistory->getId() ."\n");

// We update the city history entry with a differente init date

$cityHistory->setInitDate(date_create('2000-10-10'));

$dao->update($cityHistory);

// We search for the new init date

$cityHistory = $dao->getById($cityHistory->getId());

print ("New city history entry date found is ". DBPostgres::formatDate($cityHistory->getInitDate()) ."\n");

// We delete the new city history entry

$dao->delete($cityHistory);*/
