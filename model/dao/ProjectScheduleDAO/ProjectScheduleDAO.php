<?php

/** File for ProjectScheduleDAO
 *
 *  This file just contains {@link ProjectScheduleDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/ProjectScheduleVO.php');
include_once('phpreport/model/dao/BaseDAO.php');

/** DAO for Project Schedules
 *
 *  This is the base class for all types of Project Schedule DAOs responsible for working with data from Project Schedule table, providing a common interface.
 *  <br><br>It's important to understand that Project Schedule is a weak entity whose existence relies on the relationship Works between User and Project
 *  (obviously you won't have a schedule for a user on a project that he/she isn't working on).
 *
 * @see DAOFactory::getProjectScheduleDAO(), ProjectScheduleVO, WorksDAO
 */
abstract class ProjectScheduleDAO extends BaseDAO{

    /** Project Schedule DAO constructor.
     *
     * This is the base constructor of Project Schedule DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Project Schedule retriever by id.
     *
     * This function retrieves the row from Project Schedule table with the id <var>$projectScheduleId</var> and
     * creates a {@link ProjectScheduleVO} with its data.
     *
     * @param int $projectScheduleId the id of the row we want to retrieve.
     * @return ProjectScheduleVO a value object {@link ProjectScheduleVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($projectScheduleId);

    /** Project Schedules retriever by User id and Project id.
     *
     * This function retrieves the rows from Project table that are associated with the User with
     * the id <var>$userId</var> and the Project with <var>$projectId</var> and creates a {@link ProjectScheduleVO} with data from each row.
     *
     * @param int $userId the id of the User whose Project Schedules we want to retrieve.
     * @param int $projectId the id of the Project whose Project Schedules we want to retrieve.
     * @return array an array with value objects {@link ProjectScheduleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserProjectIds($userId, $projectId);

    /** Project Schedules retriever by User id, Project id, init week and init year.
     *
     * This function retrieves the row from Project table that is associated with the User with
     * the id <var>$userId</var> and the Project with <var>$projectId</var>, and that is scheduled to start on week <var>$initWeek</var>
     * of year <var>$initYear</var>, and creates a {@link ProjectScheduleVO} with data from the row.
     *
     * @param int $userId the id of the User whose Project Schedules we want to retrieve.
     * @param int $projectId the id of the Project whose Project Schedules we want to retrieve.
     * @param int $initWeek the init week whose Project Schedules we want to retrieve.
     * @param int $initYear the init year whose Project Schedules we want to retrieve.
     * @return ProjectScheduleVO a value object {@link ProjectScheduleVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getByUserProjectIdsDate($userId, $projectId, $initWeek, $initYear);

    /** Project Schedules retriever.
     *
     * This function retrieves all rows from Project Schedule table and creates a {@link ProjectScheduleVO} with data from each row.
     *
     * @return array an array with value objects {@link ProjectScheduleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */
    public abstract function getAll();

    /** Project Schedule updater.
     *
     * This function updates the data of a Project Schedule by its {@link ProjectScheduleVO}.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(ProjectScheduleVO $projectScheduleVO);

    /** Project Schedule creator.
     *
     * This function creates a new row for a Project Schedule by its {@link ProjectScheduleVO}.
     * The internal id of <var>$projectScheduleVO</var> will be set after its creation.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(ProjectScheduleVO $projectScheduleVO);

    /** Project Schedule deleter.
     *
     * This function deletes the data of a Project Schedule by its {@link ProjectScheduleVO}.
     *
     * @param ProjectScheduleVO $projectScheduleVO the {@link ProjectScheduleVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(ProjectScheduleVO $projectScheduleVO);

}
