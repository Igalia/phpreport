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


/** File for IterationDAO
 *
 *  This file just contains {@link IterationDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Iterations
 *
 *  This is the base class for all types of Iteration DAOs responsible for working with data from Iteration table, providing a common interface.
 *
 * @see DAOFactory::getIterationDAO(), IterationVO
 */
abstract class IterationDAO extends BaseDAO{

    /** Iteration DAO constructor.
     *
     * This is the base constructor of Iteration DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Iteration retriever by id.
     *
     * This function retrieves the row from Iteration table with the id <var>$iterationId</var> and creates a {@link IterationVO} with its data.
     *
     * @param int $iterationId the id of the row we want to retrieve.
     * @return IterationVO a value object {@link IterationVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($iterationId);

    /** Iterations retriever.
     *
     * This function retrieves all rows from Iteration table and creates a {@link IterationVO} with data from each row.
     *
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Stories retriever by Iteration id for PostgreSQL.
     *
     * This function retrieves the rows from Story table that are assigned through relationship Contains to the Iteration with
     * the id <var>$iterationId</var> and creates a {@link StoryVO} with data from each row.
     *
     * @param int $iterationId the id of the Iteration whose Stories we want to retrieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see StoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getStories($iterationId);

    /** Iteration updater.
     *
     * This function updates the data of a Iteration by its {@link IterationVO}.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function update(IterationVO $iterationVO);

    /** Iteration creator.
     *
     * This function creates a new row for a Iteration by its {@link IterationVO}.
     * The internal id of <var>$iterationVO</var> will be set after its creation.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(IterationVO $iterationVO);

    /** Iteration deleter.
     *
     * This function deletes the data of a Iteration by its {@link IterationVO}.
     *
     * @param IterationVO $iterationVO the {@link IterationVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}, {@link SQLUniqueViolationException}
     */
    public abstract function delete(IterationVO $iterationVO);

    /** Iterations retriever by Project id.
     *
     * This function retrieves the rows from Iteration table that are associated with the Project with
     * the id <var>$projectId</var> and creates an {@link IterationVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Iterations we want to retrieve.
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByProjectId($projectId);

}
