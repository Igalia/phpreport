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


/** File for ModuleDAO
 *
 *  This file just contains {@link ModuleDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/ModuleVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Modules
 *
 *  This is the base class for all types of Module DAOs responsible for working with data from Module table, providing a common interface.
 *
 * @see DAOFactory::getModuleDAO(), ModuleVO
 */
abstract class ModuleDAO extends BaseDAO{

    /** Module DAO constructor.
     *
     * This is the base constructor of Module DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Module retriever by id.
     *
     * This function retrieves the row from Module table with the id <var>$moduleId</var> and creates a {@link ModuleVO} with its data.
     *
     * @param int $moduleId the id of the row we want to retrieve.
     * @return ModuleVO a value object {@link ModuleVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($moduleId);

    /** Modules retriever.
     *
     * This function retrieves all rows from Module table and creates a {@link ModuleVO} with data from each row.
     *
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Sections retriever by Module id for PostgreSQL.
     *
     * This function retrieves the rows from Story table that are assigned through relationship Contains to the Module with
     * the id <var>$moduleId</var> and creates a {@link StoryVO} with data from each row.
     *
     * @param int $moduleId the id of the Module whose Sections we want to retrieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see StoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getSections($moduleId);

    /** Module updater.
     *
     * This function updates the data of a Module by its {@link ModuleVO}.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function update(ModuleVO $moduleVO);

    /** Module creator.
     *
     * This function creates a new row for a Module by its {@link ModuleVO}.
     * The internal id of <var>$moduleVO</var> will be set after its creation.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(ModuleVO $moduleVO);

    /** Module deleter.
     *
     * This function deletes the data of a Module by its {@link ModuleVO}.
     *
     * @param ModuleVO $moduleVO the {@link ModuleVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}, {@link SQLUniqueViolationException}
     */
    public abstract function delete(ModuleVO $moduleVO);

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
    public abstract function getByProjectId($projectId);

}
