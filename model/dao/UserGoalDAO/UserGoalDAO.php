<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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


/** File for UserGoalDAO
 *
 *  This file just contains {@link UserGoalDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 */

include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/BaseDAO.php');

/** DAO for UserGoal
 *
 *  This is the base class for all types of User Goal DAOs responsible for working with data from user goals table, providing a common interface.
 *
 * @see DAOFactory::getUserGoalDAO(), UserGoalVO
 */
abstract class UserGoalDAO extends BaseDAO{

    /** User Goal DAO constructor.
     *
     * This is the base constructor of User Goals DAOs, and it just calls its parent's constructor.
     *
     * @throws {@link ConnectionErrorException}
     * @see BaseDAO::__construct()
     */
    protected function __construct() {
        parent::__construct();
    }

    /** User Goals retriever by id.
     *
     * This function retrieves the row from User Goals table with the id <var>$userGoalId</var> and creates a {@link UserGoalVO} with its data.
     *
     * @param int $userGoalId the id of the row we want to retrieve.
     * @return UserGoalVO a value object {@link UserGoalVO} with its properties set to the values from the row.
     * @throws {@link OperationErrorException}
     */
    public abstract function getById($userGoalId);

    /** User Goal retriever by User id.
     *
     * This function retrieves the rows from User Goal table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link UserGoalVO} with data from each row.
     *
     * @param int $userId the id of the User whose User Goals we want to retrieve.
     * @return array an array with value objects {@link UserGoalVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link OperationErrorException}
     */

    public abstract function getByUserId($userId);

    /** User Goal updater.
     *
     * This function updates the data of a City History by its {@link UserGoalVO}.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function update(UserGoalVO $userGoalVO);

    /** User Goal creator.
     *
     * This function creates a new row for a User Goal by its {@link UserGoalVO}. The internal id of <var>$userGoalVO</var> will be set after its creation.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}, {@link SQLUniqueViolationException}
     */
    public abstract function create(UserGoalVO $userGoalVO);

    /** User Goal deleter.
     *
     * This function deletes the data of a User Goal by its {@link UserGoalVO}.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link OperationErrorException}
     */
    public abstract function delete(UserGoalVO $userGoalVO);

    /** User Goals fetcher for a given date
     *
     *  This function selects all user goals for a given user for a given date
     *
     * @param $userId int the id of the user
     * @param DateTime $date
     * @return mixed
     */
    public abstract function getUserGoalsForCurrentDate($userId, DateTime $date);
}
