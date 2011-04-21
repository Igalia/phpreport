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


/** File for StoryDAO
 *
 *  This file just contains {@link StoryDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Storys
 *
 *  This is the base class for all types of Story DAOs responsible for working with data from Story table, providing a common interface.
 *
 * @see DAOFactory::getStoryDAO(), StoryVO
 */
abstract class StoryDAO extends BaseDAO{

    /** Story DAO constructor.
     *
     * This is the base constructor of Story DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
    parent::__construct();
    }

    /** Story retriever by id.
     *
     * This function retrieves the row from Story table with the id <var>$storyId</var> and creates a {@link StoryVO} with its data.
     *
     * @param int $storyId the id of the row we want to retrieve.
     * @return StoryVO a value object {@link StoryVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($storyId);

    /** Storys retriever.
     *
     * This function retrieves all rows from Story table and creates a {@link StoryVO} with data from each row.
     *
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** TaskStories retriever by Story id for PostgreSQL.
     *
     * This function retrieves the rows from TaskStory table that are assigned through relationship Contains to the Story with
     * the id <var>$storyId</var> and creates a {@link TaskStoryVO} with data from each row.
     *
     * @param int $storyId the id of the Story whose TaskStories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see TaskStoryDAO
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getTaskStories($storyId);

    /** Story updater.
     *
     * This function updates the data of a Story by its {@link StoryVO}.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(StoryVO $storyVO);

    /** Story creator.
     *
     * This function creates a new row for a Story by its {@link StoryVO}.
     * The internal id of <var>$storyVO</var> will be set after its creation.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(StoryVO $storyVO);

    /** Story deleter.
     *
     * This function deletes the data of a Story by its {@link StoryVO}.
     *
     * @param StoryVO $storyVO the {@link StoryVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(StoryVO $storyVO);

    /** Stories retriever by Iteration id.
     *
     * This function retrieves the rows from Story table that are associated with the Iteration with
     * the id <var>$iterationId</var> and creates an {@link StoryVO} with data from each row.
     *
     * @param int $iterationId the id of the Iteration whose Stories we want to retrieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByIterationId($iterationId);

}
