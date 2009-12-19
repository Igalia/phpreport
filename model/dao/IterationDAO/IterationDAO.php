<?php

/** File for IterationDAO
 *
 *  This file just contains {@link IterationDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/vo/IterationVO.php');
include_once('phpreport/model/dao/BaseDAO.php');

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
