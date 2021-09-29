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


/** File for PostgreSQLUserGoalDAO
 *
 *  This file just contains {@link PostgreSQLUserGoalDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserGoalVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/UserGoalDAO/UserGoalDAO.php');

/** DAO for User Goal in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link UserGoalDAO}.
 *
 * @see UserGoalDAO, UserGoalVO
 */
class PostgreSQLUserGoalDAO extends UserGoalDAO{

    /** User Goal DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link UserGoalDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see UserGoalDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** User Goal value object constructor for PostgreSQL.
     *
     * This function creates a new {@link UserGoalVO} with data retrieved from database.
     *
     * @param array $row an array with the User Goals values from a row.
     * @return UserGoalVO a {@link UserGoalVO} with its properties set to the values from <var>$row</var>.
     * @see UserGoalVO
     */
    protected function setValues($row) {
        $userGoalVO = new UserGoalVO();

        $userGoalVO->setId($row['id']);
        $userGoalVO->setInitDate(date_create($row['init_date']));
        $userGoalVO->setUserId($row['usrid']);
        if (is_null($row['end_date'])) {
            $userGoalVO->setEndDate( null );
        }
        else {
            $userGoalVO->setEndDate(date_create($row['end_date']));
        }

        $userGoalVO->setExtraHours($row['extra_hours']);

        return $userGoalVO;
    }

    /** User Goal retriever by id for PostgreSQL.
     *
     * This function retrieves the row from User Goal table with the id <var>$userGoalId</var> and creates a {@link UserGoalVO} with its data.
     *
     * @param int $userGoalId the id of the row we want to retrieve.
     * @return UserGoalVO a value object {@link UserGoalVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($userGoalId) {
        if (!is_numeric($userGoalId)) {
            throw new SQLIncorrectTypeException($userGoalId);
        } else {
            $sql = "SELECT * FROM user_goals WHERE id=" . $userGoalId;
            $result = $this->execute($sql);
            return $result[0] ?? NULL;
        }
    }

    /** User Goal retriever by User id for PostgreSQL.
     *
     * This function retrieves the rows from user_goals table that are associated with the User with
     * the id <var>$userId</var> and creates a {@link UserGoalVO} with data from each row.
     *
     * @param int $userId the id of the User whose goals we want to retrieve.
     * @return array an array with value objects {@link UserGoalVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserId($userId) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM user_goals WHERE usrid=" . $userId . " ORDER BY id ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** User Goal updater for PostgreSQL.
     *
     * This function updates the data of a User Goal by its {@link UserGoalVO}.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to update on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function update(UserGoalVO $userGoalVO) {

        $affectedRows = 0;

        if($userGoalVO->getId() >= 0) {
            $currUserGoalVO = $this->getById($userGoalVO->getId());
        }


        // If the query returned a row then update
        if(isset($currUserGoalVO)) {
            $sql = "UPDATE user_goals SET init_date=" . DBPostgres::formatDate($userGoalVO->getInitDate()) . ", usrid=" . DBPostgres::checkNull($userGoalVO->getUserId()) . ", extra_hours=" . DBPostgres::checkNull($userGoalVO->getExtraHours()) . ", end_date=" . DBPostgres::formatDate($userGoalVO->getEndDate()) . " WHERE id=".$userGoalVO->getId();
            $res = pg_query($this->connect, $sql);

            if ($res == NULL) {
                throw new SQLQueryErrorException( pg_last_error() );
            }

            $affectedRows = pg_affected_rows($res);
        }

        return $affectedRows;

    }

    /** User Goal creator for PostgreSQL.
     *
     * This function creates a new row for a User Goal by its {@link UserGoalVO}. The internal id of <var>$userGoalVO</var> will be set after its creation.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    public function create(UserGoalVO $userGoalVO) {

        $affectedRows = 0;

        $sql = "INSERT INTO user_goals (init_date, end_date, usrid, extra_hours ) VALUES (" . DBPostgres::formatDate($userGoalVO->getInitDate()) . ", " . DBPostgres::formatDate($userGoalVO->getEndDate()) . ", " . DBPostgres::checkNull($userGoalVO->getUserId()) . ", " . DBPostgres::checkNull($userGoalVO->getExtraHours()) . ")";

        $res = pg_query($this->connect, $sql);

        if ($res == NULL)
            throw new SQLQueryErrorException(pg_last_error());

        $userGoalVO->setId(DBPostgres::getId($this->connect, "user_goals_id_seq"));

        $affectedRows = pg_affected_rows($res);

        return $affectedRows;

    }

    /** User Goal deleter for PostgreSQL.
     *
     * This function deletes the data of a User Goal by its {@link UserGoalVO}.
     *
     * @param UserGoalVO $userGoalVO the {@link UserGoalVO} with the data we want to delete from database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}
     */
    public function delete(UserGoalVO $userGoalVO) {
        $affectedRows = 0;

        // Check for a city history entry ID.
        if($userGoalVO->getId() >= 0) {
            $currentUserGoalVO = $this->getById($userGoalVO->getId());
        }

        // If it exists, then delete.
        if(isset($currUserGoalVO)) {
            $sql = "DELETE FROM user_goals WHERE id=".$userGoalVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
            $affectedRows = pg_affected_rows($res);
    }

        return $affectedRows;
    }

    /** User Goals fetcher for a given date
     *
     *  This function selects all user goals for a given user for a given date
     *
     * @param $userId int the id of the user
     * @param DateTime $date
     * @return mixed
     * @throws {@link SQLQueryErrorException}
     */
    public function getUserGoalsForCurrentDate($userId, DateTime $date) {
        if (!is_numeric($userId)) {
            throw new SQLIncorrectTypeException( $userId );
        }

        $sql = "SELECT * FROM user_goals WHERE usrid = " . $userId . "
                AND init_date <= " . DBPostgres::formatDate($date) . "
                AND end_date >= " . DBPostgres::formatDate($date) . ";";

        $result = $this->execute($sql);

        if (empty($result))
            return NULL;
        return $result[0];
    }
}
