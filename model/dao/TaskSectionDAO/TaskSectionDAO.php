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


/** File for TaskSectionDAO
 *
 *  This file just contains {@link TaskSectionDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/TaskSectionVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Task Sections
 *
 *  This is the base class for all types of Task Section DAOs responsible for working with data from TaskSection table, providing a common interface.
 *
 * @see DAOFacection::getTaskSectionDAO(), TaskSectionVO
 */
abstract class TaskSectionDAO extends BaseDAO{

    /** TaskSection DAO constructor.
     *
     * This is the base constructor of Task Section DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** TaskSection retriever by id.
     *
     * This function retrieves the row from Task Section table with the id <var>$taskSectionId</var> and creates a {@link TaskSectionVO} with its data.
     *
     * @param int $taskSectionId the id of the row we want to retrieve.
     * @return TaskSectionVO a value object {@link TaskSectionVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($taskSectionId);

    /** TaskSections retriever.
     *
     * This function retrieves all rows from Task Section table and creates a {@link TaskSectionVO} with data from each row.
     *
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** TaskSections retriever by Story id.
     *
     * This function retrieves the rows from TaskSection table that are associated with the Story with
     * the id <var>$storyId</var> (through their Project) and creates a {@link TaskSectionVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose Task Sections we want to retrieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByStoryId($storyId);

    /** TaskSection updater.
     *
     * This function updates the data of a Task Section by its {@link TaskSectionVO}.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(TaskSectionVO $taskSectionVO);

    /** TaskSection creator.
     *
     * This function creates a new row for a Task Section by its {@link TaskSectionVO}.
     * The internal id of <var>$taskSectionVO</var> will be set after its creation.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(TaskSectionVO $taskSectionVO);

    /** TaskSection deleter.
     *
     * This function deletes the data of a Task Section by its {@link TaskSectionVO}.
     *
     * @param TaskSectionVO $taskSectionVO the {@link TaskSectionVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(TaskSectionVO $taskSectionVO);

    /** TaskSections retriever by Section id.
     *
     * This function retrieves the rows from TaskSection table that are associated with the Section with
     * the id <var>$sectionId</var> and creates an {@link TaskSectionVO} with data from each row.
     *
     * @param int $sectionId the id of the Section whose TaskSections we want to retrieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getBySectionId($sectionId);

}
