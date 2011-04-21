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


/** File for PostgreSQLSectionDAO
 *
 *  This file just contains {@link PostgreSQLSectionDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/SectionDAO/SectionDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Sections in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link SectionDAO}.
 *
 * @see SectionDAO, SectionVO
 */
class PostgreSQLSectionDAO extends SectionDAO{

    /** Section DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link SectionDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see SectionDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Section value object constructor for PostgreSQL.
     *
     * This function creates a new {@link SectionVO} with data retrieved from database.
     *
     * @param array $row an array with the Section values from a row.
     * @return SectionVO a {@link SectionVO} with its properties set to the values from <var>$row</var>.
     * @see SectionVO
     */
    protected function setValues($row)
    {

        $sectionVO = new SectionVO();

        $sectionVO->setId($row[id]);
        $sectionVO->setText($row[text]);
        if ($row[accepted] == 't')
            $sectionVO->setAccepted(true);
        elseif ($row[accepted] == 'f')
            $sectionVO->setAccepted(false);
        $sectionVO->setName($row[name]);
        $sectionVO->setUserId($row[usrid]);
        $sectionVO->setModuleId($row[moduleid]);

        return $sectionVO;
    }

    /** Section retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Section table with the id <var>$sectionId</var> and creates a {@link SectionVO} with its data.
     *
     * @param int $sectionId the id of the row we want to retrieve.
     * @return SectionVO a value object {@link SectionVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($sectionId) {
        if (!is_numeric($sectionId))
        throw new SQLIncorrectTypeException($sectionId);
        $sql = "SELECT * FROM section WHERE id=".$sectionId;
    $result = $this->execute($sql);
    return $result[0];
    }

    /** Sections retriever by Module id.
     *
     * This function retrieves the rows from Section table that are associated with the Module with
     * the id <var>$moduleId</var> and creates an {@link SectionVO} with data from each row.
     *
     * @param int $moduleId the id of the Module whose Sections we want to retrieve.
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public function getByModuleId($moduleId) {
    if (!is_numeric($moduleId))
        throw new SQLIncorrectTypeException($moduleId);
        $sql = "SELECT * FROM section WHERE moduleid=".$moduleId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Sections retriever for PostgreSQL.
     *
     * This function retrieves all rows from Section table and creates a {@link SectionVO} with data from each row.
     *
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll() {
        $sql = "SELECT * FROM section ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** TaskSections retriever by Section id for PostgreSQL.
     *
     * This function retrieves the rows from TaskSection table that are assigned through relationship Contains to the Section with
     * the id <var>$sectionId</var> and creates a {@link TaskSectionVO} with data from each row.
     *
     * @param int $sectionId the id of the Section whose TaskSections we want to retrieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskSectionDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getTaskSections($sectionId) {

        $dao = DAOFactory::getTaskSectionDAO();
        return $dao->getBySectionId($sectionId);

    }

    /** Section updater for PostgreSQL.
     *
     * This function updates the data of a Section by its {@link SectionVO}.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(SectionVO $sectionVO) {
        $affectedRows = 0;

        if($sectionVO->getId() != "") {
            $currSectionVO = $this->getById($sectionVO->getId());
        }

        // If the query returned a row then update
        if(sizeof($currSectionVO) > 0) {

            $sql = "UPDATE section SET name=" . DBPostgres::checkStringNull($sectionVO->getName()) . ", accepted=" . DBPostgres::boolToString($sectionVO->getAccepted()) . ", usrid=" . DBPostgres::checkNull($sectionVO->getUserId()) . ", text =" . DBPostgres::checkStringNull($sectionVO->getText()) . ", moduleid=" . DBPostgres::checkNull($sectionVO->getmoduleId()) . " WHERE id=".$sectionVO->getId();

            $res = pg_query($this->connect, $sql);

            if ($res == NULL)
                if (strpos(pg_last_error(), "unique_section_module_name"))
                    throw new SQLUniqueViolationException(pg_last_error());
                else throw new SQLQueryErrorException(pg_last_error());

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;
    }

    /** Section creator for PostgreSQL.
     *
     * This function creates a new row for a Section by its {@link SectionVO}.
     * The internal id of <var>$sectionVO</var> will be set after its creation.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(SectionVO $sectionVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO section (name, accepted, text, usrid, moduleid) VALUES(" . DBPostgres::checkStringNull($sectionVO->getName()) . ", " . DBPostgres::boolToString($sectionVO->getAccepted()) . ", " . DBPostgres::checkStringNull($sectionVO->getText()) . ", " . DBPostgres::checkNull($sectionVO->getUserId()) . ", " . DBPostgres::checkNull($sectionVO->getModuleId()) .")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            if (strpos(pg_last_error(), "unique_section_module_name"))
                throw new SQLUniqueViolationException(pg_last_error());
            else throw new SQLQueryErrorException(pg_last_error());

        $sectionVO->setId(DBPostgres::getId($this->connect, "section_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** Section deleter for PostgreSQL.
     *
     * This function deletes the data of a Section by its {@link SectionVO}.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(SectionVO $sectionVO) {
        $affectedRows = 0;

        // Check for a section ID.
        if($sectionVO->getId() >= 0) {
            $currSectionVO = $this->getById($sectionVO->getId());
        }

        // Otherwise delete a section.
        if(sizeof($currSectionVO) > 0) {
            $sql = "DELETE FROM section WHERE id=".$sectionVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }
}




/*//Uncomment these lines in order to do a simple test of the Dao


$dao = new PostgreSQLSectionDAO();

// We create a new section

$section = new SectionVO();

$section->setName("Very well");
$section->setModuleId(1);

$dao->create($section);

print ("New section Id is ". $section->getId() ."\n");

// We search for the old text

$section = $dao->getById($section->getId());

print ("Old text found is ". $section->getName() ."\n");

// We update the module with a different text

$section->setName("New text");

$dao->update($section);

// We search for the new text

$section = $dao->getById($section->getId());

print ("New text found is ". $section->getName() ."\n");

// We delete the new section

//$dao->delete($section);*/
