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


/** File for PostgreSQLModuleDAO
 *
 *  This file just contains {@link PostgreSQLModuleDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/SQLUniqueViolationException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ModuleDAO/ModuleDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Modules in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link ModuleDAO}.
 *
 * @see ModuleDAO, ModuleVO
 */
class PostgreSQLModuleDAO extends ModuleDAO{

    /** Module DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link ModuleDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see ModuleDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Module value object constructor for PostgreSQL.
     *
     * This function creates a new {@link ModuleVO} with data retrieved from database.
     *
     * @param array $row an array with the Module values from a row.
     * @return ModuleVO a {@link ModuleVO} with its properties set to the values from <var>$row</var>.
     * @see ModuleVO
     */
    protected function setValues($row)
    {

    $moduleVO = new ModuleVO();

    $moduleVO->setId($row[id]);
    $moduleVO->setName($row[name]);
    $moduleVO->setInit(date_create($row[init]));
    $moduleVO->setEnd(date_create($row[_end]));
    $moduleVO->setSummary($row[summary]);
    $moduleVO->setProjectId($row[projectid]);

    return $moduleVO;
    }

    /** Module retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Module table with the id <var>$moduleId</var> and creates a {@link ModuleVO} with its data.
     *
     * @param int $moduleId the id of the row we want to retrieve.
     * @return ModuleVO a value object {@link ModuleVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($moduleId) {
        if (!is_numeric($moduleId))
        throw new SQLIncorrectTypeException($moduleId);
        $sql = "SELECT * FROM module WHERE id=".$moduleId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Modules retriever by Project id.
     *
     * This function retrieves the rows from Module table that are associated with the Project with
     * the id <var>$projectId</var> and creates an {@link ModuleVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Modules we want to retrieve.
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByProjectId($projectId) {
    if (!is_numeric($projectId))
        throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT * FROM module WHERE projectid=".$projectId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Sections retriever by Module id for PostgreSQL.
     *
     * This function retrieves the rows from Section table that are assigned through relationship Contains to the Module with
     * the id <var>$moduleId</var> and creates a {@link SectionVO} with data from each row.
     *
     * @param int $moduleId the id of the Module whose Sections we want to retrieve.
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see SectionDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getSections($moduleId) {

        $dao = DAOFactory::getSectionDAO();
        return $dao->getByModuleId($moduleId);

    }

    /** Modules retriever for PostgreSQL.
     *
     * This function retrieves all rows from Module table and creates a {@link ModuleVO} with data from each row.
     *
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM module ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Module updater for PostgreSQL.
     *
     * This function updates the data of a Module by its {@link ModuleVO}.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(ModuleVO $moduleVO) {
        $affectedRows = 0;

        if($moduleVO->getId() != "") {
            $currModuleVO = $this->getById($moduleVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currModuleVO) > 0) {

            $sql = "UPDATE module SET name=" . DBPostgres::checkStringNull($moduleVO->getName()) . ", init=" . DBPostgres::formatDate($moduleVO->getInit()) . ", _end=" . DBPostgres::formatDate($moduleVO->getEnd()) . ", summary=" . DBPostgres::checkStringNull($moduleVO->getSummary()) . ", projectid=" . DBPostgres::checkNull($moduleVO->getProjectId()) . " WHERE id=".$moduleVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_module_project_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Module creator for PostgreSQL.
     *
     * This function creates a new row for a Module by its {@link ModuleVO}.
     * The internal id of <var>$moduleVO</var> will be set after its creation.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(ModuleVO $moduleVO) {

    $affectedRows = 0;

    $sql = "INSERT INTO module (name, init, _end, summary, projectid) VALUES(" . DBPostgres::checkStringNull($moduleVO->getName()) . ", " . DBPostgres::formatDate($moduleVO->getInit()) . ", " . DBPostgres::formatDate($moduleVO->getEnd()) . ", " . DBPostgres::checkStringNull($moduleVO->getSummary()) . ", " . DBPostgres::checkNull($moduleVO->getProjectId()) .")";

    $res = pg_query($this->connect, $sql);

    if ($res == NULL)
        if (strpos(pg_last_error(), "unique_module_project_name"))
            throw new SQLUniqueViolationException(pg_last_error());
        else throw new SQLQueryErrorException(pg_last_error());

    $moduleVO->setId(DBPostgres::getId($this->connect, "module_id_seq"));

    $affectedRows = pg_affected_rows($res);

    return $affectedRows;

    }

    /** Module deleter for PostgreSQL.
     *
     * This function deletes the data of a Module by its {@link ModuleVO}.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(ModuleVO $moduleVO) {
        $affectedRows = 0;

        // Check for a module ID.
        if($moduleVO->getId() >= 0) {
            $currModuleVO = $this->getById($moduleVO->getId());
        }

        // Otherwise delete a module.
        if(sizeof($currModuleVO) > 0) {
            $sql = "DELETE FROM module WHERE id=".$moduleVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




//Uncomment these lines in order to do a simple test of the Dao


/*$dao = new PostgreSQLModuleDAO();

// We create a new module

$module = new ModuleVO();

$module->setInit(date_create('2009-01-05'));
$module->setEnd(date_create('2009-01-15'));
$module->setName("Very well");
$module->setSummary("Old text");
$module->setProjectId(1);

$dao->create($module);*/

/*print ("New module Id is ". $module->getId() ."\n");

// We search for the old text

$module = $dao->getById($module->getId());

print ("Old text found is ". $module->getName() ."\n");

// We update the module with a different text

$module->setName("New text");

$dao->update($module);

// We search for the new text

$module = $dao->getById($module->getId());

print ("New text found is ". $module->getName() ."\n");

// We delete the new module

//$dao->delete($module);*/
