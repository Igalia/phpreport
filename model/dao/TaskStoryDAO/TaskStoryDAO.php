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


/** File for TaskStoryDAO
 *
 *  This file just contains {@link TaskStoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Task Stories
 *
 *  This is the base class for all types of Task Story DAOs responsible for working with data from TaskStory table, providing a common interface.
 *
 * @see DAOFactory::getTaskStoryDAO(), TaskStoryVO
 */
abstract class TaskStoryDAO extends BaseDAO{

    /** TaskStory DAO constructor.
     *
     * This is the base constructor of Task Story DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** TaskStory retriever by id.
     *
     * This function retrieves the row from Task Story table with the id <var>$taskStoryId</var> and creates a {@link TaskStoryVO} with its data.
     *
     * @param int $taskStoryId the id of the row we want to retrieve.
     * @return TaskStoryVO a value object {@link TaskStoryVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($taskStoryId);

    /** TaskStorys retriever.
     *
     * This function retrieves all rows from Task Story table and creates a {@link TaskStoryVO} with data from each row.
     *
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Open TaskStories retriever for PostgreSQL.
     *
     * This function retrieves all rows from TaskStory table that don't have an ending date assigned and creates
     * a {@link TaskStoryVO} with data from each row. We can pass optional parameters for filtering by User, <var>$userId</var>,
     * and by Project, <var>$projectId</var>.
     *
     * @param int $userId optional parameter for filtering by User.
     * @param int $projectId optional parameter for filtering by Project.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getOpen($userId = NULL, $projectId = NULL);

    /** TaskStory updater.
     *
     * This function updates the data of a Task Story by its {@link TaskStoryVO}.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(TaskStoryVO $taskStoryVO);

    /** TaskStory creator.
     *
     * This function creates a new row for a Task Story by its {@link TaskStoryVO}.
     * The internal id of <var>$taskStoryVO</var> will be set after its creation.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(TaskStoryVO $taskStoryVO);

    /** TaskStory deleter.
     *
     * This function deletes the data of a Task Story by its {@link TaskStoryVO}.
     *
     * @param TaskStoryVO $taskStoryVO the {@link TaskStoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(TaskStoryVO $taskStoryVO);

    /** TaskStories retriever by Story id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Story with
     * the id <var>$storyId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose TaskStories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByStoryId($storyId);

    /** TaskStories retriever by Task Section id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Task Section with
     * the id <var>$taskSectionId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $taskSectionId the id of the Task Section whose Task Stories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByTaskSectionId($taskSectionId);

    /** TaskStories retriever by Section id.
     *
     * This function retrieves the rows from TaskStory table that are associated with the Section with
     * the id <var>$sectionId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $sectionId the id of the Section whose Task Stories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getBySectionId($sectionId);

}
