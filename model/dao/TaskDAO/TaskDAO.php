<?php
/*
 * Copyright (C) 2009-2013 Igalia, S.L. <info@igalia.com>
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


/** File for TaskDAO
 *
 *  This file just contains {@link TaskDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/vo/DirtyTaskVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for Tasks
 *
 *  This is the base class for all types of Task DAOs responsible for working with data from Task table, providing a common interface.
 *
 * @see DAOFactory::getTaskDAO(), TaskVO
 */
abstract class TaskDAO extends BaseDAO{

    /** The valid values for the group fields of function {@link getTaskReport()}.
     *
     * This variable contains an array of the valid values for the two optional group fields of {@link getTaskReport()},
     * associating each string with the proper value for ordering in database.
     *
     * @var array
     * @see getTaskReport()
     */
    protected $groupFields = array("USER" => "usrid", "PROJECT" => "projectid", "CUSTOMER" => "customerid", "TTYPE" => "ttype", "STORY" => "story");

    /** Task retriever by id.
     *
     * This function retrieves the row from Task table with the id <var>$taskId</var> and creates a {@link TaskVO} with its data.
     *
     * @param int $taskId the id of the row we want to retrieve.
     * @return TaskVO a value object {@link TaskVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($taskId);

    /** Work Personal Summary retriever.
     *
     * This function retrieves the amount of hours the User with id <var>$userId<var> has worked on
     * the day <var>$date</var>, its week and its month.
     *
     * @param int $userId the id of the User whose summary we want to retrieve.
     * @param DateTime $date the date on which we want to retrieve the summary.
     * @return array an array with the values related to the keys 'day', 'week' and 'month'.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getPersonalSummary($userId, DateTime $date);

    /** Tasks retriever by User id and date.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and for date <var>$date</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @param DateTime $date the date whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their init time.
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getByUserIdDate($userId, DateTime $date);

    /** Task User id checker.
     *
     * This function retrieves the row from Task table with id <var>$taskId</var> and checks if it's User id
     * is the same as <var>$userId</var>.
     *
     * @param int $taskId the id of the Task we want to check.
     * @param int $userId the User id we want to compare.
     * @return bool a bool indicating if the User id is the same.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function checkTaskUserId($taskId, $userId);

    /** Tasks retriever by multiple fields.
     *
     * This function retrieves a subset of rows from Task table and creates a
     * {@link TaskVO} with data from each row.
     *
     * Multiple fields can be used as filters; to disable a filter, a NULL value
     * has to be passed on that parameter.
     *
     * @param DateTime $filterStartDate start date to filter tasks. Those tasks
     *        having a date equal or later than this one will be returned. NULL
     *        to deactivate filtering by this field.
     * @param DateTime $filterEndDate end date to filter tasks. Those tasks
     *        having a date equal or sooner than this one will be returned. NULL
     *        to deactivate filtering by this field.
     * @param boolean $telework filter tasks by their telework field.
     *        NULL to deactivate filtering by this field.
     * @param boolean $onsite filter tasks by their onsite field.
     *        NULL to deactivate filtering by this field.
     * @param string $filterText string to filter tasks by their description
     *        field. Tasks with a description that contains this string will
     *        be returned. NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only projects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     * @param int $userId id of the user whose tasks will be filtered. NULL to
     *        deactivate filtering by this field.
     * @param int $projectId id of the project which tasks will be filtered by.
     *        NULL to deactivate filtering by this field.
     * @param int $customerId id of the customer whose tasks will be filtered.
     *        NULL to deactivate filtering by this field.
     * @param int $taskStoryId id of the story inside the XP tracker which tasks
     *        will be filtered. NULL to deactivate filtering by this field.
     * @param string $filterStory string to filter tasks by their story field.
     *        Tasks with a story that contains this string will be returned.
     *        NULL to deactivate filtering by this field.
     * @param boolean $emptyText filter tasks by the presence, or absence, of
     *        text in the description field. NULL to deactivate this field; if
     *        not NULL, the parameter $filterText will be ignored.
     * @param boolean $emptyStory filter tasks by the presence, or absence, of
     *        text in the story field. NULL to deactivate this field; if
     *        not NULL, the parameter $filterStory will be ignored.
     * @return array an array with value objects {@link TaskVO} with their
     *         properties set to the values from the rows and ordered
     *         ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getFiltered($filterStartDate = NULL,
            $filterEndDate = NULL, $telework = NULL, $onsite = NULL, $filterText = NULL,
            $type = NULL, $userId = NULL, $projectId = NULL, $customerId = NULL,
            $taskStoryId = NULL, $filterStory = NULL, $emptyText = NULL, $emptyStory = NULL);

    /** Tasks report generator.
     *
     * This function generates a report of the hours users have worked in Tasks related to an element <var>$reportObject</var>,
     * which may be a {@link UserVO}, {@link TaskVO} or {@link CustomerVO}. Two optional dates can also be passed, <var>$initDate</var>
     * and <var>$endDate</var>, to limit the dates of the tasks retrieved (if they are not passed, it returns the result for tasks of any date),
     * and two group fields, <var>$groupField1</var> and <var>$groupField2</var>, that only are used for making groups with the
     * results if they are passed.
     *
     * @param mixed $reportObject the object whose related Tasks we want to use for computing the extra hours.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @param string $groupField1 the optional first field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField2 the optional second field for grouping the data (valid values are stored in {@link $groupFields}).
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields if they were passed).
     * @throws {@link OperationErrorException}
     */
    public abstract function getTaskReport($reportObject, DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1 = NULL, $groupField2 = NULL);

    /** Tasks global report generator.
     *
     * This function generates a report of the hours users have worked in all Tasks. Two optional dates can also be passed, <var>$initDate</var>
     * and <var>$endDate</var>, to limit the dates of the tasks retrieved (if they are not passed, it returns the result for tasks of any date),
     * and three group fields, <var>$groupField1</var>, <var>$groupField2</var> and <var>$groupField3</var>, that only are used for making groups with the
     * results (first is mandatory, the other two are optional) if they are passed. This function works very likely {@link getTaskReport()}, but for all tasks.
     *
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @param string $groupField1 the mandatory first field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField2 the optional second field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField3 the optional third field for grouping the data (valid values are stored in {@link $groupFields}).
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields).
     * @todo write examples of usage and result.
     * @throws {@link TaskReportInvalidParameterException}
     * @throws {@link SQLQueryErrorException}
     */
    public abstract function getGlobalTaskReport(DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1, $groupField2 = NULL, $groupField3 = NULL);

    /** Weekly hours worked on project by users generator.
     *
     * @param ProjectVO $project
     * @param DateTime|null $initDate
     * @param DateTime|null $endDate
     * @return mixed
     */
    public abstract function getProjectUserWeeklyWorkingHours(ProjectVO $project, DateTime $initDate = NULL, DateTime $endDate = NULL);
    /** Vacations report generator.
     *
     * This function generates a report of the vacations hours a user {@link UserVO} has spent as for today. Two optional DateTime parameters can be passed,
     * <var>$initDate</var> and <var>$endDate</var>, to limit the dates of the vacation hours retrieved.
     *
     * @param UserVO $userVO the user whose vacation hours we want to retrieve.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @return array an associative array with the user id (<i>usrid</i>) and the vacations hours he/she has spent (<i>add_hours</i>).
     * @throws {@link OperationErrorException}
     */
    public abstract function getVacations(UserVO $userVO, DateTime $initDate = NULL, DateTime $endDate = NULL);

    public abstract function getVacationsDates(UserVO $userVO, DateTime $initDate = NULL, DateTime $endDate = NULL): array;

    /** Task partial updater for PostgreSQL.
     *
     * This function updates only some fields of the data of a Task using a
     * {@link DirtyTaskVO} to know the data and the information of which fields
     * should be updated.
     *
     * @param DirtyTaskVO $taskVO the {@link TaskVO} with the data we want to
     *        update on database and the information about which fields must be
     *        updated.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public abstract function partialUpdate(DirtyTaskVO $taskVO);

    /** Task batch partial updater.
     *
     * Equivalent to {@see partialUpdate} for arrays of tasks.
     *
     * @param array $tasks array of {@link DirtyTaskVO} objects to be updated.
     * @return array OperationResult the array of {@link OperationResult} with information about operation
     */
    public abstract function batchPartialUpdate($tasks);

    /** Task creator.
     *
     * This function creates a new row for a Task by its {@link TaskVO}.
     * The internal id of <var>$taskVO</var> will be set after its creation.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to insert on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public abstract function create(TaskVO $taskVO);

    /** Task batch creator.
     *
     * Equivalent to {@see create} for arrays of tasks.
     *
     * @param array $tasks array of {@link TaskVO} objects to be created.
     * @return array OperationResult the array of {@link OperationResult} with information about operation status
     */
    public abstract function batchCreate($tasks);

    /** Task deleter.
     *
     * This function deletes the data of a Task by its {@link TaskVO}.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(TaskVO $taskVO);

    /** Retrieve the date of the last task for a user
     *
     * @param int $userId Id of the user to check
     * @param  $referenceDate The date to start searching backwars.
     * @return DateTime The date of the last task of that user before $referenceDate,
     *         or NULL if the user doesn't have any tasks yet.
     */
    public abstract function getLastTaskDate($userId, DateTime $referenceDate);

    /** Retrieve the id for the configured VACATIONS_PROJECT.
     *
     * The project id will be retrieved based on the configuration parameters
     * VACATIONS_PROJECT_ID and VACATIONS_PROJECT, in that order. The latter is
     * considered deprecated and will log a warning.
     * @return int The ID of the configured VACATIONS_PROJECT or null, if it's
     * not properly set up.
     */
    public abstract function getVacationsProjectId(): ?int;
}
