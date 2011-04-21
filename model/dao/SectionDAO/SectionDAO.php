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


/** File for SectionDAO
 *
 *  This file just contains {@link SectionDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/SectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Sections
 *
 *  This is the base class for all types of Section DAOs responsible for working with data from Section table, providing a common interface.
 *
 * @see DAOFactory::getSectionDAO(), SectionVO
 */
abstract class SectionDAO extends BaseDAO{

    /** Section DAO constructor.
     *
     * This is the base constructor of Section DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Section retriever by id.
     *
     * This function retrieves the row from Section table with the id <var>$sectionId</var> and creates a {@link SectionVO} with its data.
     *
     * @param int $sectionId the id of the row we want to retrieve.
     * @return SectionVO a value object {@link SectionVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($sectionId);

    /** Sections retriever.
     *
     * This function retrieves all rows from Section table and creates a {@link SectionVO} with data from each row.
     *
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

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
    public abstract function getTaskSections($sectionId);

    /** Section updater.
     *
     * This function updates the data of a Section by its {@link SectionVO}.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(SectionVO $sectionVO);

    /** Section creator.
     *
     * This function creates a new row for a Section by its {@link SectionVO}.
     * The internal id of <var>$sectionVO</var> will be set after its creation.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(SectionVO $sectionVO);

    /** Section deleter.
     *
     * This function deletes the data of a Section by its {@link SectionVO}.
     *
     * @param SectionVO $sectionVO the {@link SectionVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(SectionVO $sectionVO);

    /** Sections retriever by Module id.
     *
     * This function retrieves the rows from Section table that are associated with the Module with
     * the id <var>$moduleId</var> and creates a {@link SectionVO} with data from each row.
     *
     * @param int $moduleId the id of the Module whose Sections we want to retrieve.
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByModuleId($moduleId);

}
