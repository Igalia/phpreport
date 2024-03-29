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


/** File for PostgreSQLTaskDAO
 *
 *  This file just contains {@link PostgreSQLTaskDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/TaskReportInvalidParameterException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/DirtyTaskVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomerVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/TaskDAO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/** DAO for Tasks in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link TaskDAO}.
 *
 * @see TaskDAO, TaskVO
 */
class PostgreSQLTaskDAO extends TaskDAO{

    /** Task DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link TaskDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see TaskDAO::__construct()
     */
    function __construct() {
    parent::__construct();
    }

    /** Task value object constructor for PostgreSQL.
     *
     * This function creates a new {@link TaskVO} with data retrieved from database.
     *
     * @param array $row an array with the Task values from a row.
     * @return TaskVO a {@link TaskVO} with its properties set to the values from <var>$row</var>.
     * @see TaskVO
     */
    protected function setValues($row)
    {
        $taskVO = new TaskVO();

        $taskVO->setId($row['id']);
        $taskVO->setDate(date_create($row['_date']));
        $taskVO->setInit($row['init']);
        $taskVO->setEnd($row['_end']);
        $taskVO->setStory($row['story']);
        if (strtolower($row['telework']) == "t")
            $taskVO->setTelework(True);
        elseif (strtolower($row['telework']) == "f")
            $taskVO->setTelework(False);
        if (strtolower($row['onsite']) == "t")
            $taskVO->setOnsite(True);
        elseif (strtolower($row['onsite']) == "f")
            $taskVO->setOnsite(False);
        $taskVO->setText($row['text']);
        $taskVO->setTtype($row['ttype']);
        $taskVO->setPhase($row['phase']);
        $taskVO->setUserId($row['usrid']);
        $taskVO->setProjectId($row['projectid']);
        $taskVO->setUpdatedAt($row['updated_at']);

        return $taskVO;
    }

    /** Task retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Task table with the id
     * <var>$taskId</var> and creates a {@link TaskVO} with its data.
     *
     * @param int $taskId the id of the row we want to retrieve.
     * @return TaskVO a value object {@link TaskVO} with its properties set to
     * the values from the row, or NULL if no task was found.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($taskId) {
        if (!is_numeric($taskId))
            throw new SQLIncorrectTypeException($taskId);
        $sql = "SELECT * FROM task WHERE id=".$taskId;
        $result = $this->execute($sql);

        if (empty($result))
            return NULL;
        return $result[0];
    }

    /** Tasks retriever by User id and date for PostgreSQL.
     *
     * This function retrieves the rows from Task table that are associated with the User with
     * the id <var>$userId</var> and for date <var>$date</var> and creates a {@link TaskVO} with data from each row.
     *
     * @param int $userId the id of the User whose Tasks we want to retrieve.
     * @param DateTime $date the date whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their init time.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getByUserIdDate($userId, DateTime $date) {
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT * FROM task WHERE usrid=" . $userId . " AND _date=" . DBPostgres::formatDate($date) . " ORDER BY init ASC";
    $result = $this->execute($sql);
    return $result;
    }

    /** Task User id checker for PostgreSQL.
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
    public function checkTaskUserId($taskId, $userId) {
    if (!is_numeric($taskId))
        throw new SQLIncorrectTypeException($taskId);
    if (!is_numeric($userId))
        throw new SQLIncorrectTypeException($userId);
        $sql = "SELECT " . $userId . " = (SELECT usrid FROM task WHERE id=" . $taskId . ") as same";

    $res = @pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    $row = @pg_fetch_array($res);
    if (strtolower($row['same']) == "t")
        return true;

    return false;
    }

    /** Work Personal Summary retriever for PostgreSQL.
     *
     * This function retrieves the amount of minutes the User with id <var>$userId<var> has worked on
     * the day <var>$date</var>, its week and its month.
     *
     * @param int $userId the id of the User whose summary we want to retrieve.
     * @param DateTime $date the date on which we want to retrieve the summary.
     * @return array an array with the values related to the keys 'day', 'week' and 'month'.
     * @throws {@link SQLIncorrectTypeException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getPersonalSummary($userId, DateTime $date) {
        if (!is_numeric($userId))
            throw new SQLIncorrectTypeException($userId);
        //this is a query grouping three unrelated queries
        $sql = "SELECT * FROM

            -- this query selects the hours worked in the current week
            (SELECT COALESCE(SUM(_end-init), 0) AS WEEK
                FROM task
                WHERE usrid=" . $userId  . "
                    AND _date >=
                        --calculate the first day of the week
                        (timestamp " . DBPostgres::formatDate($date)  . " -
                            ((int2(date_part('dow',timestamp " .
                                DBPostgres::formatDate($date)  . ")+7-1) % 7) ||' days')
                            ::interval)::date
                    AND _date <=
                        --calculate the last day of the week
                        (timestamp " . DBPostgres::formatDate($date)  . " -
                            ((int2(date_part('dow',timestamp " .
                                DBPostgres::formatDate($date)  . ")+7-1) % 7)-6 ||' days')
                            ::interval)::date
            ) a ,

            -- this query selects the hours worked in the current month
            (SELECT COALESCE(SUM(_end-init), 0) AS MONTH
                FROM task
                WHERE usrid=" . $userId . "
                    AND EXTRACT(MONTH FROM _date) =
                        EXTRACT(MONTH FROM date " . DBPostgres::formatDate($date)  . ")
                    AND EXTRACT(YEAR FROM _date) =
                        EXTRACT(YEAR FROM date " . DBPostgres::formatDate($date) . ")
            ) b ,

            -- this query selects the hours worked in the current day
            (SELECT COALESCE(SUM(_end-init), 0) AS day
                FROM task
                WHERE usrid=" . $userId  . "
                    AND _date = " . DBPostgres::formatDate($date) . "
            ) c;";

        $res = @pg_query($this->connect, $sql);

        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++)
            {
                $rows[$i] = @pg_fetch_array($res);
            }
        }

        return $rows[0];
    }

    public function getFiltered($filterStartDate = NULL, $filterEndDate = NULL,
            $telework = NULL, $onsite = NULL, $filterText = NULL, $type = NULL,
            $userId = NULL, $projectId = NULL, $customerId = NULL, $taskStoryId = NULL,
            $filterStory = NULL, $emptyText = NULL, $emptyStory = NULL) {

        $conditions = "TRUE";
        if ($filterStartDate != NULL) {
            $conditions .= " AND (_date >= ".
                DBPostgres::formatDate($filterStartDate) . " OR _date is NULL)";
        }
        if ($filterEndDate != NULL) {
            $conditions .= " AND (_date <= ".
                DBPostgres::formatDate($filterEndDate) . " OR _date is NULL)";
        }
        if ($telework !== NULL) {
            $conditions .= " AND telework = " .
                    DBPostgres::boolToString($telework);
        }
        if ($onsite !== NULL) {
            $conditions .= " AND onsite = " .
                    DBPostgres::boolToString($onsite);
        }
        if ($filterText != NULL && $emptyText === NULL) {
            $conditions .= " AND text like ('%$filterText%')";
        }
        if ($type != NULL) {
            $conditions .= " AND ttype = '$type'";
        }
        if ($userId != NULL) {
            $conditions .= " AND usrid = $userId";
        }
        if ($projectId != NULL) {
            $conditions .= " AND projectid = $projectId";
        }
        if ($customerId != NULL) {
            $conditions .= " AND project.customerid = $customerId";
        }

        if ($filterStory != NULL && $emptyStory === NULL) {
            $conditions .= " AND story like ('%$filterStory%')";
        }
        if ($emptyText !== NULL) {
            if ($emptyText) {
                $conditions .= " AND (text = '' OR text IS NULL)";
            }
            else {
                $conditions .= " AND (text != '' OR text IS NOT NULL)";
            }
        }
        if ($emptyStory !== NULL) {
            if ($emptyStory) {
                $conditions .= " AND (story = '' OR story IS NULL)";
            }
            else {
                $conditions .= " AND (story != '' OR story IS NOT NULL)";
            }
        }
        $sql = "SELECT task.* FROM task LEFT JOIN project ON task.projectid=project.id
                WHERE $conditions ORDER BY id ASC";
        return $this->execute($sql);
    }

    /** Tasks report generator for PostgreSQL.
     *
     * This function generates a report of the hours users have worked in Tasks related to an element <var>$reportObject</var>,
     * which may be a {@link UserVO}, {@link ProjectVO} or {@link CustomerVO}. Two optional dates can also be passed, <var>$initDate</var>
     * and <var>$endDate</var>, to limit the dates of the tasks retrieved (if they are not passed, it returns the result for tasks of any date),
     * and two group fields, <var>$groupField1</var> and <var>$groupField2</var>, that only are used for making groups with the
     * results if they are passed.
     *
     * @param mixed $reportObject the object whose related Tasks we want to use for computing the worked hours.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @param string $groupField1 the optional first field for grouping the data (valid values are stored in {@link $groupFields}).
     * @param string $groupField2 the optional second field for grouping the data (valid values are stored in {@link $groupFields}).
     * @return array an array with the resulting rows of computing the extra hours as associative arrays (they contain a field
     * <i>add_hours</i> with that result and fields for the grouping fields if they were passed).
     * @todo write examples of usage and result.
     * @throws {@link TaskReportInvalidParameterException}
     * @throws {@link SQLQueryErrorException}
     */
    public function getTaskReport($reportObject, DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1 = NULL, $groupField2 = NULL) {

    $sql = "SELECT ";

    if (isset($this->groupFields[$groupField1]))
    {
        $sql = $sql . $this->groupFields[$groupField1] . ", ";
        if (isset($this->groupFields[$groupField2]))
            $sql = $sql . $this->groupFields[$groupField2] . ", ";
        elseif (!is_null($groupField2))
            throw new TaskReportInvalidParameterException($groupField2);
    }
    elseif (!is_null($groupField1))
        throw new TaskReportInvalidParameterException($groupField1);

    $sql = $sql . "SUM( _end - init ) / 60.0 AS add_hours FROM task ";

    if ($reportObject instanceof UserVO)
    {
        $sql = $sql . "WHERE usrid=" . $reportObject->getId() . " ";
    }
    elseif ($reportObject instanceof CustomerVO)
    {
        $sql = $sql . "WHERE customerid=" . $reportObject->getId() . " ";
    }
    elseif ($reportObject instanceof ProjectVO)
    {
        $sql = $sql . "WHERE projectid=" . $reportObject->getId() . " ";
    }
    else throw new TaskReportInvalidParameterException($reportObject);

    if (!is_null($initDate) && !is_null($endDate))
    {
        $sql = $sql . "AND _date >= " . DBPostgres::formatDate($initDate) . " AND _date <= " . DBPostgres::formatDate($endDate) . " ";
    }

    if (isset($this->groupFields[$groupField1]))
    {
        $sql = $sql . "GROUP BY " . $this->groupFields[$groupField1];
        if (isset($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];

        $sql = $sql . " ORDER BY " . $this->groupFields[$groupField1];
        if (isset($this->groupFields[$groupField2]))
            $sql = $sql . ", " . $this->groupFields[$groupField2];

    }

    $res = @pg_query($this->connect, $sql);

    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    $rows = array();
    for($i = 0; $i < pg_num_rows($res); $i++) {
        $rows[$i] = @pg_fetch_array($res);
    }

    return $rows;

    }

    /** Tasks global report generator for PostgreSQL.
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
    public function getGlobalTaskReport(DateTime $initDate = NULL, DateTime $endDate = NULL, $groupField1, $groupField2 = NULL, $groupField3 = NULL) {

    $sql = "SELECT ";

    if (!is_null($groupField1))
    {
        if (isset($this->groupFields[$groupField1]))
        {
            $sql = $sql . $this->groupFields[$groupField1] . ", ";
            if (!is_null($groupField2))
            {
                if (isset($this->groupFields[$groupField2]))
                {
                    $sql = $sql . $this->groupFields[$groupField2] . ", ";
                    if (!is_null($groupField3))
                    {
                        if(isset($this->groupFields[$groupField3]))
                            $sql = $sql . $this->groupFields[$groupField3] . ", ";
                        elseif (!is_null($groupField3))
                            throw new TaskReportInvalidParameterException($groupField3);
                    }
                } elseif (!is_null($groupField2))
                    throw new TaskReportInvalidParameterException($groupField2);
            }
        }
        else throw new TaskReportInvalidParameterException($groupField1);
    } else throw new TaskReportInvalidParameterException($groupField1);

    $sql = $sql . "SUM( _end - init ) / 60.0 AS add_hours FROM task ";

    if (!is_null($initDate) && !is_null($endDate))
    {
        $sql = $sql . "WHERE _date >= " . DBPostgres::formatDate($initDate) . " AND _date <= " . DBPostgres::formatDate($endDate) . " ";
    }

    $sql = $sql . "GROUP BY ";

    $sql = $sql . $this->groupFields[$groupField1];
    if (!is_null($groupField2))
    {
        $sql = $sql . ", " . $this->groupFields[$groupField2];
        if (!is_null($groupField3))
            $sql = $sql . ", " . $this->groupFields[$groupField3];
    }

    $sql = $sql . " ORDER BY ";

    $sql = $sql . $this->groupFields[$groupField1];
    if (!is_null($groupField2))
    {
        $sql = $sql . ", " . $this->groupFields[$groupField2];
        if (!is_null($groupField3))
            $sql = $sql . ", " . $this->groupFields[$groupField3];
    }

    $res = @pg_query($this->connect, $sql);

    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $rows = array();
        for($i = 0; $i < pg_num_rows($res); $i++)
        {
            $rows[$i] = @pg_fetch_array($res);
        }

        return $rows;
    }

    /** Weekly hours worked on project by users for PostgreSQL
     *
     * @param ProjectVO $projectVO
     * @param DateTime|null $initDate
     * @param DateTime|null $endDate
     * @return mixed
     * @throws SQLQueryErrorException
     */
    public function getProjectUserWeeklyWorkingHours(ProjectVO $projectVO, DateTime $initDate = NULL, DateTime $endDate = NULL){
        $projectId = $projectVO->getId();

        if( $initDate && $endDate ) {
            // Given a start date and end date, only pick weeks in between
            $sql = "select usrid,
                  EXTRACT(WEEK FROM _date) AS week,
                  EXTRACT(ISOYEAR FROM _date) AS year,
                  COALESCE(SUM(_end-init), 0) AS total_hours
                  from task WHERE projectid = $projectId
                  AND
                    _date >= (timestamp " . DBPostgres::formatDate($initDate)  . ")
                  AND
                    _date <= (timestamp " . DBPostgres::formatDate($endDate)  . ")
                  GROUP BY year, week, usrid";
        } else {
            $sql = "select usrid,
            EXTRACT(WEEK FROM _date) AS week,
            EXTRACT(ISOYEAR FROM _date) AS year,
            COALESCE(SUM(_end-init), 0) AS total_hours
            from task WHERE projectid = $projectId
            GROUP BY year, week, usrid";
        }

        $res = pg_query($this->connect, $sql);

        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $rows = array();
        for($i = 0; $i < pg_num_rows($res); $i++)
        {
            $rows[$i] = @pg_fetch_array($res);
        }

        return $rows;
    }

    /**
     * This variable caches the result produced by getVacationsProjectId().
     * @see getVacationsProjectId().
     */
    private ?int $vacationsProjectId = null;

    /** Retrieve the id for the configured VACATIONS_PROJECT.
     *
     * The project id will be retrieved based on the configuration parameters
     * VACATIONS_PROJECT_ID or VACATIONS_PROJECT, in that order. The latter is
     * considered deprecated and will log a warning.
     * @return int The ID of the configured VACATIONS_PROJECT or null, if it's
     * not properly set up.
     */
    public function getVacationsProjectId(): ?int {
        try {
            return ConfigurationParametersManager::getParameter('VACATIONS_PROJECT_ID');
        }
        catch (UnknownParameterException $e) {
            error_log("The VACATIONS_PROJECT configuration parameter will be deprecated. Use VACATIONS_PROJECT_ID instead.");
        }

        if (!is_null($this->vacationsProjectId)) {
            return $this->vacationsProjectId;
        }
        $sql = "SELECT * FROM project WHERE description='" . ConfigurationParametersManager::getParameter('VACATIONS_PROJECT') . "'";

        $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());
        $resultAux = pg_fetch_array($res);

        if (!$resultAux) {
            //the project configured as VACATIONS_PROJECT doesn't exist
            return null;
        }
        $this->vacationsProjectId = $resultAux['id'];
        return $this->vacationsProjectId;
    }

    /** Vacations report generator for PostgreSQL.
     *
     * This function generates a report of the vacations hours a user {@link UserVO} has spent as for today. Two optional DateTime parameters can be passed,
     * <var>$initDate</var> and <var>$endDate</var>, to limit the dates of the vacation hours retrieved.
     *
     * @param UserVO $userVO the user whose vacation hours we want to retrieve.
     * @param DateTime $initDate the optional DateTime object that represents the beginning of the date interval.
     * @param DateTime $endDate the optional DateTime object that represents the end of the date interval (included).
     * @return array an associative array with the user id (<i>usrid</i>) and the vacations hours he/she has spent (<i>add_hours</i>).
     * @throws {@link SQLQueryErrorException}
     */
    public function getVacations(UserVO $userVO, DateTime $initDate = NULL, DateTime $endDate = NULL) {
    $vacId = $this->getVacationsProjectId();

    if (is_null($vacId)) {
        //the project configured as VACATIONS_PROJECT doesn't exist
        return null;
    }

    $sql = "SELECT usrid, SUM(_end-init)/60.0 AS add_hours, max(updated_at) as updated_at FROM task WHERE projectid=" . $vacId ." AND usrid=" . $userVO->getId();

    if (!is_null($initDate))
    {
        $sql = $sql . " AND _date >=" . DBPostgres::formatDate($initDate);
        if (!is_null($endDate))
            $sql = $sql . " AND _date <=" . DBPostgres::formatDate($endDate);
    }

    $sql = $sql . " GROUP BY usrid";

    $res = pg_query($this->connect, $sql);
    if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

    $result = array();
    for($i = 0; $i < pg_num_rows($res); $i++) {
        $result[$i] = @pg_fetch_array($res);
    }

    if (empty($result))
        return NULL;
    return $result[0];

    }

    public function getVacationsDates(UserVO $userVO, DateTime $initDate = NULL, DateTime $endDate = NULL): array
    {
        $projectId = $this->getVacationsProjectId();

        if (is_null($projectId)) {
            return [];
        }

        $sql = "SELECT _date, init, _end FROM task WHERE projectid=" . $projectId . " AND usrid=" . $userVO->getId();

        if (!is_null($initDate)) {
            $sql .= " AND _date >=" . DBPostgres::formatDate($initDate);
            if (!is_null($endDate))
                $sql .= " AND _date <=" . DBPostgres::formatDate($endDate);
        }

        $sql .= " ORDER BY _date";

        $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $result = array();
        for ($i = 0; $i < pg_num_rows($res); $i++) {
            $row = @pg_fetch_array($res);
            $result[$row['_date']] = [
                'init' => $row['init'],
                'end' => $row['_end']
            ];
        }

        if (empty($result))
            return [];
        return $result;
    }

    /** Task partial updater for PostgreSQL.
     *
     * This function updates only some fields of the data of a Task using a
     * {@link DirtyTaskVO} to know the data and the information of which fields
     * should be updated.
     * WARNING: it doesn't check if task overlaps with other tasks, because that
     * would be very expensive to do for every task. TaskDAO::batchPartialUpdate
     * should be used for that purpose.
     * TODO: consider making private.
     *
     * @param DirtyTaskVO $taskVO the {@link TaskVO} with the data we want to
     *        update on database and the information about which fields must be
     *        updated.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public function partialUpdate(DirtyTaskVO $taskVO) {
        $result = new OperationResult(false);

        if($taskVO->getId() != "") {
            $currTaskVO = $this->getById($taskVO->getId());
        }

        // If the query returned a row then update
        if(isset($currTaskVO)) {

        $sql = "UPDATE task SET ";

        if ($taskVO->isDateDirty())
            $sql .= "_date=" .
                    DBPostgres::formatDate($taskVO->getDate()) . ", ";

        if ($taskVO->isInitDirty())
            $sql .= "init=" . DBPostgres::checkNull($taskVO->getInit()) . ", ";

        if ($taskVO->isEndDirty())
            $sql .= "_end=" .
                    DBPostgres::checkNull($taskVO->getEnd()) . ", ";

        if ($taskVO->isStoryDirty())
            $sql .= "story=" .
                    DBPostgres::checkStringNull($taskVO->getStory()) . ", ";

        if ($taskVO->isTeleworkDirty())
            $sql .= "telework=" .
                    DBPostgres::boolToString($taskVO->getTelework()) . ", ";

        if ($taskVO->isOnsiteDirty())
            $sql .= "onsite=" .
                    DBPostgres::boolToString($taskVO->getOnsite()) . ", ";

        if ($taskVO->isTextDirty())
            $sql .= "text=" .
                    DBPostgres::checkStringNull($taskVO->getText()) . ", ";

        if ($taskVO->isTtypeDirty())
            $sql .= "ttype=" .
                    DBPostgres::checkStringNull($taskVO->getTtype()) . ", ";

        if ($taskVO->isPhaseDirty())
            $sql .= "phase=" .
                    DBPostgres::checkStringNull($taskVO->getPhase()) . ", ";

        if ($taskVO->isUserIdDirty())
            $sql .= "usrid=" .
                    DBPostgres::checkNull($taskVO->getUserId()) . ", ";

        if ($taskVO->isProjectIdDirty())
            $sql .= "projectid=" .
                    DBPostgres::checkNull($taskVO->getProjectId()) . ", ";

        if (strlen($sql) == strlen("UPDATE task SET "))
        return NULL;

        $last = strrpos($sql, ",");

        if ($last == (strlen($sql) - 2))
        $sql = substr($sql, 0, -2);

        $sql = $sql . ", updated_at=now() WHERE id=".$taskVO->getId();

            $res = pg_query($this->connect, $sql);
        if ($res == NULL) {
            $errorMessage = pg_last_error();
            $resultMessage = "Task update failed:\n";
            if(strpos($errorMessage, "end_after_init_task")) {
                $resultMessage .= "Start time later than end time.";
                $result->setResponseCode(400);
            }
            else {
                $resultMessage .= $errorMessage;
                $result->setResponseCode(500);
            }
            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
        }
        else if (pg_affected_rows($res) == 1) {
            $result->setIsSuccessful(true);
            $result->setMessage('Task updated successfully.');
            $result->setResponseCode(201);
        }
        }

        return $result;
    }

    /** Update a batch of DirtyTaskVO objects.
     *
     * This function is used for partially updating an array of Tasks. It will
     * check there is no overlapping between existing tasks and updated ones.
     * It uses {@link DirtyTaskVO} to know the data and the information of which
     * fields should be updated.
     *
     * @param array $tasks the Task value objects we want to update. Must be
     *        DirtyTaskVO objects which contain also the information about
     *        which fields must be updated.
     * @return array OperationResult the array of {@link OperationResult} with information about operation status
     */
    public function batchPartialUpdate($tasks) {
        if (!$this->checkOverlappingWithDBTasks($tasks)) {
            $result = new OperationResult(false);
            $result->setErrorNumber(10);
            $result->setMessage("Task update failed:\nDetected overlapping times.");
            $result->setResponseCode(400);
            return array($result);
        }

        $results = array();
        foreach ($tasks as $task) {
            $results[] = $this->partialUpdate($task);
        }

        return $results;
    }

    /**
     * Checks if the set of task modifications overlaps with the set of tasks
     * that are already saved.
     * PRECONDITION: we assume all the tasks belong to the same user.
     * @param {array} $tasks set of modifications. It can contain {@link TaskVO}
              objects for new tasks, or {@link DirtyTaskVO} objects for updates.
     * @return {boolean} true if there is no overlapping.
     */
    private function checkOverlappingWithDBTasks($tasks) {
        if (count($tasks) == 0) {
            return true;
        }

        //group tasks by date
        //at the same time, update TaskVO objects
        $tasksByDate = array();
        $updatedTaskIds = array();
        foreach ($tasks as $task) {
            //add normal task
            if ($task->isNew()) {
                $date = $task->getDate()->format('Y-m-d');
                $tasksByDate[$date][] = $task;
            }
            //update dirty task
            else if ($task->isDirty()) {
                $originalTask = $this->getById($task->getId());
                $originalTask->updateFrom($task);
                $date = $originalTask->getDate()->format('Y-m-d');
                $tasksByDate[$date][] = $originalTask;
                $updatedTaskIds[] = $task->getId();
            }
        }

        //evaluate every date independently
        $userId = $tasks[array_key_first($tasks)]->getUserId();
        foreach (array_keys($tasksByDate) as $index) {
            $date = $tasksByDate[$index][0]->getDate();

            //get the tasks already saved for that date
            $tasksInDB = $this->getByUserIdDate($userId, $date);

            //remove dirty tasks which have already been updated
            foreach ($tasksInDB as $key => $task) {
                if (in_array($task->getId(), $updatedTaskIds)) {
                    unset($tasksInDB[$key]);
                }
            }

            //check overlapping
            if (!$this->checkOverlappingTasks(
                    array_merge($tasksByDate[$index], $tasksInDB))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if some Task in a set of Task objects overlaps with the other
     * objects.
     * PRECONDITION: it assumes all tasks belong to the same user and have the
     * same date.
     * @param {array} $tasks set of {@link TaskVO} objects.
     * @return {boolean} true if there is no overlapping.
     */
    private function checkOverlappingTasks($tasks) {
        //if array is empty or has only one element: there is no overlapping
        if (count($tasks) <= 1) {
            return true;
        }

        //set init as the index of the array
        $indexes = array();
        foreach ($tasks as $task) {
            if (in_array($task->getInit(), $indexes)) {
                //when two tasks share the same init time
                return false;
            }
            $indexes[] = $task->getInit();
        }
        $sortedTasks = array_combine($indexes, $tasks);

        //sort array per its index (init time), then reset indexes
        ksort($sortedTasks);
        $sortedTasks = array_values($sortedTasks);

        //compare the end of one task with the beginning of the next one
        for ($i = 1; $i < count($sortedTasks); $i++) {
            if ($sortedTasks[$i]->getInit() < $sortedTasks[$i-1]->getEnd()) {
                return false;
            }
        }

        return true;
    }

    /** Task creator for PostgreSQL.
     *
     * This function creates a new row for a Task by its {@link TaskVO}.
     * The internal id of <var>$taskVO</var> will be set after its creation.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to insert on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public function create(TaskVO $taskVO) {
        $tasks = array($taskVO);
        return $this->batchCreate($tasks)[0];
    }

    /** Task creator for PostgreSQL.
     *
     * This function creates a new row for a Task by its {@link TaskVO}.
     * The internal id of <var>$taskVO</var> will be set after its creation.
     * WARNING: it doesn't check if task overlaps with other tasks.
     * TaskDAO::create and TaskDAO::batchCreate should be used for that purpose.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to insert on database.
     * @return int the number of rows that have been affected (it should be 1).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    private function createInternal(TaskVO $taskVO) {
        $result = new OperationResult(false);

        $sql =
            "INSERT INTO task (_date, init, _end, story, telework, onsite, " .
                "text, ttype, phase, usrid, projectid, updated_at) " .
            "VALUES (:date, :init, :end, :story, :telework, :onsite, " .
                ":text, :ttype, :phase, :usrid, :projectid, now() )";

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":date", DBPostgres::formatDate($taskVO->getDate()), PDO::PARAM_STR);
            $statement->bindValue(":init", $taskVO->getInit(), PDO::PARAM_INT);
            $statement->bindValue(":end", $taskVO->getEnd(), PDO::PARAM_INT);
            $statement->bindValue(":story", $taskVO->getStory(), PDO::PARAM_STR);
            $statement->bindValue(":telework", $taskVO->getTelework(), PDO::PARAM_BOOL);
            $statement->bindValue(":onsite", $taskVO->getOnsite(), PDO::PARAM_BOOL);
            $statement->bindValue(":text", $taskVO->getText(), PDO::PARAM_STR);
            $statement->bindValue(":ttype", $taskVO->getTtype(), PDO::PARAM_STR);
            $statement->bindValue(":phase", $taskVO->getPhase(), PDO::PARAM_STR);
            $statement->bindValue(":usrid", $taskVO->getUserId(), PDO::PARAM_INT);
            $statement->bindValue(":projectid", $taskVO->getProjectId(), PDO::PARAM_INT);
            $statement->execute();

            $taskVO->setId($this->pdo->lastInsertId('task_id_seq'));

            $result->setIsSuccessful(true);
            $result->setMessage('Task created successfully.');
            $result->setResponseCode(201);
        }
        catch (PDOException $ex) {
            $errorMessage = $ex->getMessage();
            $resultMessage = "Error creating task:\n";
            if(strpos($errorMessage, "end_after_init_task")) {
                $resultMessage .= "Start time later than end time.";
                $result->setResponseCode(400);
            }
            else if(strpos($errorMessage, "Not null violation")) {
                $resultMessage .= "Missing compulsory data";
                if(!$taskVO->getInit() || !$taskVO->getEnd()) {
                    $resultMessage .= ": init and/or end time";
                }
                $resultMessage .= ".";
                $result->setResponseCode(400);
            }
            else {
                $resultMessage .= $errorMessage;
                $result->setResponseCode(500);
            }
            $result->setErrorNumber($ex->getCode());
            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
        }

        return $result;
    }

    /** Task batch creator.
     *
     * Equivalent to {@see create} for arrays of tasks.
     *
     * @param array $tasks array of {@link TaskVO} objects to be created.
     * @return array OperationResult the array of {@link OperationResult} with information about operation status
     */
    public function batchCreate($tasks) {
        if (!$this->checkOverlappingWithDBTasks($tasks)) {
            $result = new OperationResult(false);
            $result->setErrorNumber(10);
            $result->setMessage("Task creation failed:\nDetected overlapping times.");
            $result->setResponseCode(400);
            return array($result);
        }

        $results = array();
        foreach ($tasks as $task) {
            $results[] = $this->createInternal($task);
        }

        return $results;
    }

    /** Task deleter for PostgreSQL.
     *
     * This function deletes the data of a Task by its {@link TaskVO}.
     *
     * @param TaskVO $taskVO the {@link TaskVO} with the data we want to delete from database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public function delete(TaskVO $taskVO) {
        $result = new OperationResult(false);

        $sql = "DELETE FROM task WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":id", $taskVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $result->setIsSuccessful(true);
            $result->setMessage('Task deleted successfully.');
            $result->setResponseCode(200);
        }
        catch (PDOException $ex) {
            $errorMessage = $ex->getMessage();
            $resultMessage = "Task deletion failed: \n";
            // unpredictable error, just return the native error code and message
            $resultMessage .= $errorMessage;
            $result->setErrorNumber($ex->getCode());
            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
            $result->setResponseCode(500);
        }

        return $result;
    }

    public function getLastTaskDate($userId, DateTime $date, $strictBeforeDate=True) {
        $operator = ($strictBeforeDate ? "<" : "<=");
        $sql = "SELECT MAX(_date) FROM task " .
                    "WHERE usrId=" . DBPostgres::checkNull($userId) .
                    " AND _date " . $operator . " " . DBPostgres::formatDate($date);

        $res = pg_query($this->connect, $sql);
        $row = pg_fetch_result($res, 0);

        if($row == NULL)
            return NULL;

        return date_create($row);
    }

    public function getEmptyDaysInPeriod($userId, DateTime $start, DateTime $end) {
        $interval = new DateInterval('P1D');
        $end->setTime(0,0,1); // tiny hack to make sure the end date is included in the date range loop
        $dateRange = new DatePeriod($start, $interval, $end);
        $datesInPeriod = [];
        // Get weekdays for period
        foreach ($dateRange as $date) {
            if ($date->format('N') < 6) {
                $datesInPeriod[] = $date->format('Y-m-d');
            }
        }
        // Get dates that have tasks
        $sql = "SELECT DISTINCT(_date) FROM task " .
                "WHERE usrId=:userId AND _date BETWEEN :start AND :end";

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(":userId", $userId, PDO::PARAM_INT);
        $statement->bindValue(":start", DBPostgres::formatDate($start), PDO::PARAM_STR);
        $statement->bindValue(":end", DBPostgres::formatDate($end), PDO::PARAM_STR);
        $statement->execute();

        $daysWithTasks = [];
        while ($day = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $daysWithTasks[] = $day[0];
        }
        return array_diff($datesInPeriod, $daysWithTasks);
    }
}
