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


/** File for PostgreSQLAreaDAO
 *
 *  This file just contains {@link PostgreSQLAreaDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/AreaVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaDAO/AreaDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/PostgreSQLProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/AreaHistoryDAO/PostgreSQLAreaHistoryDAO.php');

/** DAO for Areas in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link AreaDAO}.
 *
 * @see AreaDAO, AreaVO
 */
class PostgreSQLAreaDAO extends AreaDAO{

    /** Area DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link AreaDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see AreaDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Area value object constructor for PostgreSQL.
     *
     * This function creates a new {@link AreaVO} with data retrieved from database.
     *
     * @param array $row an array with the Area values from a row.
     * @return AreaVO an {@link AreaVO} with its properties set to the values from <var>$row</var>.
     * @see AreaVO
     */
    protected function setValues($row)
    {

        $areaVO = new AreaVO();

        $areaVO->setId($row['id']);
        $areaVO->setName($row['name']);

    return $areaVO;
    }

    /** Area retriever by name for PostgreSQL.
     *
     * This function retrieves the row from Area table with the name <var>$areaName</var> and creates an {@link AreaVO} with its data.
     *
     * @param string $areaName the name of the row we want to retrieve.
     * @return AreaVO a value object {@link AreaVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByName($areaName) {
        $sql = "SELECT * FROM area WHERE name=" . $areaName;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Area retriever by Id for PostgreSQL.
     *
     * This function retrieves the row from Area table with the id <var>$areaId</var> and creates an {@link AreaVO} with its data.
     *
     * @param int $areaId the id of the row we want to retrieve.
     * @return AreaVO a value object {@link AreaVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($areaId) {
    if (!is_numeric($areaId))
        throw new SQLIncorrectTypeException($areaId);
        $sql = "SELECT * FROM area WHERE id=" . $areaId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Project retriever by Area id for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are assigned to the Area with
     * the id <var>$areaId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectDAO::getByAreaId()
     * @throws {@link SQLQueryErrorException}
     */
    public function getProjects($areaId) {

    $dao = DAOFactory::getProjectDAO();
    return $dao->getByAreaId($areaId);

    }

    /** Area Histories retriever by Area id for PostgreSQL.
     *
     * This function retrieves the rows from AreaHistory table that are assigned to the Area with
     * the id <var>$areaId</var> and creates an {@link AreaHistoryVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Area Histories we want to retrieve.
     * @return array an array with value objects {@link AreaHistoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see AreaHistoryDAO::getByAreaId()
     * @throws {@link SQLQueryErrorException}
     */
    public function getAreaHistories($areaId) {

    $dao = DAOFactory::getAreaHistoryDAO();
    return $dao->getByAreaId($areaId);

    }

    /** Area retriever for PostgreSQL.
     *
     * This function retrieves all rows from Area table and creates an {@link AreaVO} with data from each row.
     *
     * @return array an array with value objects {@link AreaVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM area ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Area updater for PostgreSQL.
     *
     * This function updates the data of an Area by its {@link AreaVO}.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(AreaVO $areaVO) {
        $affectedRows = 0;

        if($areaVO->getId() >= 0) {
            $currareaVO = $this->getById($areaVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currareaVO) > 0) {

            $sql = "UPDATE area SET name=" . DBPostgres::checkStringNull($areaVO->getName()) . " WHERE id=".$areaVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_area_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Area creator for PostgreSQL.
     *
     * This function creates a new row for an Area by its {@link AreaVO}. The internal id of <var>$areaVO</var> will be set after its creation.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(AreaVO $areaVO) {
        $affectedRows = 0;

        $sql = "INSERT INTO area (name) VALUES (" . DBPostgres::checkStringNull($areaVO->getName()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_area_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $areaVO->setId(DBPostgres::getId($this->connect, "area_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Area deleter for PostgreSQL.
     *
     * This function deletes the data of an Area by its {@link AreaVO}.
     *
     * @param AreaVO $areaVO the {@link AreaVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(AreaVO $areaVO) {
        $affectedRows = 0;

        // Check for an area ID.
        if($areaVO->getId() >= 0) {
            $currareaVO = $this->getById($areaVO->getId());
        }

        // Delete an area.
        if(sizeof($currareaVO) > 0) {
            $sql = "DELETE FROM area WHERE id=".$areaVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao



$dao = new PostgreSQLareaDAO();

// We create a new area

$area = new areaVO();

$area->setName("Players");

$dao->create($area);

print ("New area Id is ". $area->getId() ."\n");

// We search for the new Id

$area = $dao->getById($area->getId());

print ("New area Id found is ". $area->getId() ."\n");

// We update the area with a differente name

$area->setName("Non-players");

$dao->update($area);

// We search for the new name

$area = $dao->getById($area->getId());

print ("New area name found is ". $area->getName() ."\n");

// We delete the new area

$dao->delete($area);*/
