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


/** File for PostgreSQLProjectDAO
 *
 *  This file just contains {@link PostgreSQLProjectDAO}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage DAO
 * @author Jorge López Fernández <jlopez@igalia.com>
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/util/SQLIncorrectTypeException.php');
include_once(PHPREPORT_ROOT . '/util/DBPostgres.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectDAO/ProjectDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/ProjectUserDAO/PostgreSQLProjectUserDAO.php');
include_once(PHPREPORT_ROOT . '/model/dao/TaskDAO/PostgreSQLTaskDAO.php');
include_once(PHPREPORT_ROOT . '/model/OperationResult.php');

/** DAO for Projects in PostgreSQL
 *
 *  This is the implementation for PostgreSQL of {@link ProjectDAO}.
 *
 * @see ProjectDAO, ProjectVO
 */
class PostgreSQLProjectDAO extends ProjectDAO {

    /** Project DAO for PostgreSQL constructor.
     *
     * This is the constructor of the implementation for PostgreSQL of {@link ProjectDAO}, and it just calls its parent's constructor.
     *
     * @throws {@link DBConnectionErrorException}
     * @see ProjectDAO::__construct()
     */
    function __construct() {
        parent::__construct();
    }

    /** Project value object constructor for PostgreSQL.
     *
     * This function creates a new {@link ProjectVO} with data retrieved from database.
     *
     * @param array $row an array with the Project values from a row.
     * @return ProjectVO an {@link ProjectVO} with its properties set to the values from <var>$row</var>.
     * @see ProjectVO
     */
    protected function setValues($row) {

        $projectVO = new ProjectVO();

        $projectVO->setId($row['id']);

        if (strtolower($row['activation']) == "t")
            $projectVO->setActivation(True);
        else $projectVO->setActivation(False);

        if (is_null($row['init']))
            $projectVO->setInit(NULL);
        else $projectVO->setInit(date_create($row['init']));

        if (is_null($row['_end']))
            $projectVO->setEnd(NULL);
        else $projectVO->setEnd(date_create($row['_end']));

        $projectVO->setInvoice($row['invoice']);
        $projectVO->setEstHours($row['est_hours']);
        $projectVO->setAreaId($row['areaid']);
        $projectVO->setType($row['type']);
        $projectVO->setDescription($row['description']);
        $projectVO->setMovedHours($row['moved_hours']);
        $projectVO->setSchedType($row['sched_type']);
        if(isset($row['customer_name'])) {
            $projectVO->setCustomerName($row['customer_name']);
        }
        if(isset($row['customerid'])) {
            $projectVO->setCustomerId($row['customerid']);
        }

        return $projectVO;

    }

    /** SQL retrieving data sentences performer for Custom Project.
     *
     * This function executes the retrieving data sentence in <var>$sql</var> and calls the function {@link setCustomValues()} in order to
     * create a custom value object with each row, and returning them in an array afterwards. It uses the connection stored in <var>{@link $connect}</var>.
     * It's simply an alternative to {@link execute()} for creating Custom VO on the same DAO.
     *
     * @param string $sql a simple SQL 'select' sentence as a string.
     * @return array an associative array of custom value objects created with the data retrieved with <var>$sql</var>.
     * @throws {@link SQLQueryErrorException}
     * @see setCustomValues()
     */
    protected function customExecute($sql) {
        $res = @pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $VO = array();

        if(pg_num_rows($res) > 0) {
            for($i = 0; $i < pg_num_rows($res); $i++) {
                $row = @pg_fetch_array($res);
                $VO[$i] = $this->setCustomValues($row);
            }
        }

        @pg_freeresult($res);

        return $VO;
    }


    /** Custom Project value object constructor for PostgreSQL.
     *
     * This function creates a new {@link CustomProjectVO} with data retrieved from database.
     *
     * @param array $row an array with the Project values from a row, and additional data.
     * @return CustomProjectVO an {@link CustomProjectVO} with its properties set to the values from <var>$row</var>.
     * @see CustomProjectVO
     */
    protected function setCustomValues($row) {

        $projectVO = new CustomProjectVO();

        $projectVO->setId($row['id']);

        if (strtolower($row['activation']) == "t")
            $projectVO->setActivation(True);
        else $projectVO->setActivation(False);

        if (is_null($row['init']))
            $projectVO->setInit(NULL);
        else $projectVO->setInit(date_create($row['init']));

        if (is_null($row['_end']))
            $projectVO->setEnd(NULL);
        else $projectVO->setEnd(date_create($row['_end']));

        $projectVO->setInvoice($row['invoice']);
        $projectVO->setEstHours($row['est_hours']);
        $projectVO->setAreaId($row['areaid']);
        $projectVO->setType($row['type']);
        $projectVO->setDescription($row['description']);
        $projectVO->setMovedHours($row['moved_hours']);
        $projectVO->setSchedType($row['sched_type']);
        $projectVO->setWorkedHours($row['worked_hours']);
        $projectVO->setTotalCost($row['total_cost']);
        if(isset($row['customer_name'])) {
            $projectVO->setCustomerName($row['customer_name']);
        }
        if(isset($row['customerid'])) {
            $projectVO->setCustomerId($row['customerid']);
        }
        $projectVO->setFutureLoggedHours($row['futureLoggedHours']);

        return $projectVO;

    }

    /** Project retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Project table with the id <var>$projectId</var> and creates a {@link ProjectVO} with its data.
     *
     * @param int $projectId the id of the row we want to retrieve.
     * @return ProjectVO a value object {@link ProjectVO} with its properties set to the values from the row.
     * @throws {@link SQLQueryErrorException}
     */
    public function getById($projectId) {
        if (!is_numeric($projectId))
            throw new SQLIncorrectTypeException($projectId);
        $sql = "SELECT project.*, customer.name AS customer_name
            FROM project LEFT JOIN customer ON project.customerid=customer.id
            WHERE project.id={$projectId}";
        $result = $this->execute($sql);

        if (empty($result))
            return NULL;
        return $result[0];
    }

    /** Custom Project retriever by id for PostgreSQL.
     *
     * This function retrieves the row from Project table with the id <var>$projectId</var> with additional data
     * and creates a {@link CustomProjectVO} with its data.
     *
     * @param int $projectId the id of the row we want to retrieve.
     * @return CustomProjectVO a value object {@link CustomProjectVO} with its properties set to the values from the row
     * and the additional data.
     * @throws {@link SQLQueryErrorException}
     */
    public function getCustomById($projectId) {
        if (!is_numeric($projectId))
            throw new SQLIncorrectTypeException($projectId);
        $sql =
            "SELECT project.*, customer.name AS customer_name,".
                "SUM((task._end-task.init)/60.0) AS worked_hours, ".
                "SUM(((task._end-task.init)/60.0) * hour_cost) AS total_cost ".
            "FROM project ".
                "LEFT JOIN customer ON project.customerid=customer.id LEFT JOIN task ON project.id = task.projectid ".
                "LEFT JOIN hour_cost_history ON hour_cost_history.usrid = task.usrid ".
                    "AND task._date >= hour_cost_history.init_date ".
                    "AND task._date <= hour_cost_history.end_date ".
            "WHERE project.id= {$projectId} ".
            "GROUP BY project.id, project.description, project.activation, ".
                "project.init, project._end, project.invoice, ".
                "project.est_hours, project.areaid, project.description, ".
                "project.type, project.moved_hours, project.sched_type, ".
                "project.customerid, customer.name";
        $result = $this->customExecute($sql);
        $project = $result[0];

        // Get possible hours logged in the future
        $sql =
            "SELECT CASE ".
                "WHEN COUNT(*) = 0 THEN 0 ".
                "ELSE ROUND(SUM((task._end - task.init) / 60.0),2) ".
            "END AS future_hours ".
            "FROM task ".
            "WHERE projectid=:projectId and _date > CURRENT_DATE";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(":projectId", $projectId, PDO::PARAM_INT);
        $statement->execute();
        $hours = $statement->fetch(PDO::FETCH_NUM);
        $project->setFutureLoggedHours($hours[0]);

        return $project;
    }

    /** Projects retriever by Area id for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are associated with the Area with
     * the id <var>$areaId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $areaId the id of the Area whose Projects we want to retrieve.
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getByAreaId($areaId, $orderField = 'id') {
        $sql = "SELECT * FROM project WHERE areaid=" . $areaId . " ORDER BY " . $orderField  . " ASC";
        $result = $this->execute($sql);
        return $result;
    }

    /** Users retriever by Project id (relationship ProjectUser) for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship ProjectUser to the Project with
     * the id <var>$projectId</var> and creates an {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function getUsersProject($projectId) {

        $dao = DAOFactory::getProjectUserDAO();
        return $dao->getByProjectId($projectId);

    }

     /** ProjectUser relationship entry creator by Project id and User id for PostgreSQL.
     *
     * This function creates a new entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project we want to relate to the User.
     * @param int $userId the id of the User we want to relate to the Project.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function addUserProject($projectId, $userId) {

        $dao = DAOFactory::getProjectUserDAO();
        return $dao->create($userId, $projectId);

    }

    /** ProjectUser relationship entry deleter by Project id and User id for PostgreSQL.
     *
     * This function deletes an entry in the table ProjectUser (that represents that relationship between Projects and Users)
     * with the Project id <var>$projectId</var> and the User id <var>$userId</var>.
     *
     * @param int $projectId the id of the Project whose relation to the User we want to delete.
     * @param int $userId the id of the User whose relation to the Project we want to delete.
     * @return int the number of rows that have been affected (it should be 1).
     * @see ProjectUserDAO, UserDAO
     * @throws {@link SQLQueryErrorException}
     */
    public function removeUserProject($projectId, $userId) {

        $dao = DAOFactory::getProjectUserDAO();
        return $dao->delete($userId, $projectId);

    }


    /** Projects retriever for PostgreSQL.
     *
     * This function retrieves all rows from Project table and creates a {@link ProjectVO} with data from each row.
     *
     * @param null $userLogin
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @param string $description string to filter projects by their description
     *        field. Projects with a description that contains this string will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterStartDate start date of the time filter for
     *        projects. Projects will a finish date later than this date will
     *        be returned. NULL to deactivate filtering by this field.
     * @param DateTime $filterEndDate end date of the time filter for projects.
     *        Projects will a start date sooner than this date will be returned.
     *        NULL to deactivate filtering by this field.
     * @param boolean $activation filter projects by their activation field.
     *        NULL to deactivate filtering by this field.
     * @param long $areaId value to filter projects by their area field.
     *        projects. NULL to deactivate filtering by this field.
     * @param string $type string to filter projects by their type field.
     *        Only trojects with a type field that matches completely with this
     *        string will be returned. NULL to deactivate filtering by this
     *        field.
     * @param long $customerId value to filter projects by their customer field.
     *        NULL to deactivate filtering by this field.
     * @param string $filterCname string to filter projects by their customer name. NULL
     *        to deactivate filtyering by this field
     * @param boolean $returnExtendedInfo flag to check if the response should include more information
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAll($userLogin = NULL, $active = False, $orderField = 'id', $description = NULL,
        $filterStartDate = NULL, $filterEndDate = NULL, $activation = NULL, $areaId = NULL, $type = NULL,
        $customerId = NULL, $filterCname = NULL, $returnExtendedInfo = False) {
        $userCondition = "true";
        $activeCondition = "true";
        $conditions = "TRUE";

        if ($userLogin) {
            $userCondition = "project.id IN (SELECT projectid FROM project_usr LEFT JOIN usr ON project_usr.usrid=usr.id " . "WHERE login=" . DBPostgres::checkStringNull( $userLogin ) . ") ";
        }
        if ($active) {
            $activeCondition = "activation='True'";
        }
        if ($description != null) {
            foreach(explode(" ", $description) as $word) {
                $conditions .= " AND UPPER(project.description)" . " LIKE ('%' || UPPER('$word') || '%')";
            }
        }
        if ($filterStartDate != null) {
            $conditions .= " AND (project._end >= " . DBPostgres::formatDate( $filterStartDate ) . " OR project._end is NULL)";
        }
        if ($filterEndDate != null) {
            $conditions .= " AND (project.init <= " . DBPostgres::formatDate( $filterEndDate ) . " OR project.init is NULL)";
        }
        if ($activation != NULL) {
            $conditions .= " AND project.activation = " . $activation;
        }
        if ($areaId != null) {
            $conditions .= " AND project.areaid = $areaId";
        }
        if ($type != null) {
            $conditions .= " AND project.type = '$type'";
        }
        if ($customerId != null) {
            $conditions .= " AND project.customerid = $customerId";
        }
        if ($filterCname != null) {
            foreach (explode(" ", $filterCname) as $word) {
                $conditions .= " AND UPPER(customer.name)" . " LIKE ('%' || UPPER('$word') || '%')";
            }
        }

        if ($returnExtendedInfo) {
            $sql = "SELECT project.*, customer.name AS customer_name,
                SUM((task._end-task.init)/60.0) AS worked_hours,
                SUM(((task._end-task.init)/60.0) * hour_cost) AS total_cost
                FROM project
                LEFT JOIN customer ON project.customerid=customer.id
                LEFT JOIN task ON project.id = task.projectid
                LEFT JOIN hour_cost_history ON hour_cost_history.usrid = task.usrid
                AND task._date >= hour_cost_history.init_date
                AND task._date <= hour_cost_history.end_date
                WHERE $conditions AND $activeCondition AND $userCondition
                GROUP BY project.id, project.description, project.activation,
                project.init, project._end, project.invoice, project.est_hours,
                project.areaid, project.description, project.type,
                project.moved_hours, project.sched_type, project.customerid, customer_name
                ORDER BY " . $orderField . " ASC";
            $result = $this->customExecute($sql);
        } else {
            $sql = "SELECT project.*, customer.name AS customer_name FROM
                    project LEFT JOIN customer
                    ON project.customerid=customer.id
                    WHERE " . $activeCondition . " AND " . $userCondition . " AND " . $conditions . "
                    ORDER BY $orderField ASC";
            $result = $this->execute($sql);
        }
        return $result;
    }

    /** Custom Projects retriever for PostgreSQL.
     *
     * This function retrieves all rows from Project table and creates a {@link CustomProjectVO} with data from each row,
     * and additional ones.
     *
     * @param bool $active optional parameter for obtaining only the active projects (by default it returns all them).
     * @param string $orderField optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array an array with value objects {@link CustomProjectVO} with their properties set to the values from the rows
     * and the additional data, and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    public function getAllCustom($active = False, $orderField = 'id') {
        $sql = "SELECT project.*, SUM((task._end-task.init)/60.0) AS worked_hours, SUM(((task._end-task.init)/60.0) * hour_cost) AS total_cost FROM project LEFT JOIN task ON project.id = task.projectid LEFT JOIN hour_cost_history ON hour_cost_history.usrid = task.usrid AND task._date >= hour_cost_history.init_date AND task._date <= hour_cost_history.end_date ";
        if ($active)
            $sql = $sql . " WHERE project.activation= 'True'";
        $sql = $sql . " GROUP BY project.id, project.description, project.activation, project.init, project._end, project.invoice, project.est_hours, project.areaid, project.description, project.type, project.moved_hours, project.sched_type ORDER BY project." . $orderField . " ASC";
        return $this->customExecute($sql);
    }

    public function getByDescription(string $desc): ?int
    {
        $sql = "SELECT * FROM project WHERE description='" . $desc . "'";

        $res = pg_query($this->connect, $sql);
        if ($res == NULL) throw new SQLQueryErrorException(pg_last_error());

        $resultAux = @pg_fetch_array($res);

        return $resultAux['id'] ?? NULL;
    }

    /** Project updater for PostgreSQL.
     *
     * This function updates the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to update on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public function update(ProjectVO $projectVO) {
        $result = new OperationResult(false);

        $sql = "UPDATE project SET activation=:activation" .
                ", init=:init" .
                ", _end=:end" .
                ", invoice=:invoice" .
                ", est_hours=:est_hours" .
                ", areaid=:areaid" .
                ", customerid=:customerid" .
                ", type=:type" .
                ", description=:description" .
                ", moved_hours=:moved_hours" .
                ", sched_type=:sched_type" .
                " WHERE id=:id";

        $initDateFormatted = (is_null($projectVO->getInit())) ? null : DBPostgres::formatDate($projectVO->getInit());
        $endDateFormatted = (is_null($projectVO->getEnd())) ? null : DBPostgres::formatDate($projectVO->getEnd());

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":activation", $projectVO->getActivation(), PDO::PARAM_BOOL);
            $statement->bindValue(":init", $initDateFormatted, PDO::PARAM_STR);
            $statement->bindValue(":end", $endDateFormatted, PDO::PARAM_STR);
            $statement->bindValue(":invoice", $projectVO->getInvoice(), PDO::PARAM_STR);
            $statement->bindValue(":est_hours", $projectVO->getEstHours(), PDO::PARAM_STR);
            $statement->bindValue(":areaid", $projectVO->getAreaId(), PDO::PARAM_INT);
            $statement->bindValue(":customerid", $projectVO->getCustomerId(), PDO::PARAM_INT);
            $statement->bindValue(":description", $projectVO->getDescription(), PDO::PARAM_STR);
            $statement->bindValue(":type", $projectVO->getType(), PDO::PARAM_STR);
            $statement->bindValue(":moved_hours", $projectVO->getMovedHours(), PDO::PARAM_STR);
            $statement->bindValue(":sched_type", $projectVO->getSchedType(), PDO::PARAM_STR);
            $statement->bindValue(":id", $projectVO->getId(), PDO::PARAM_INT);

            $statement->execute();

            $result->setIsSuccessful(true);
            $result->setMessage('Project created successfully.');
            $result->setResponseCode(200);
        }
        catch (PDOException $ex) {
            //make sure to log the error as a failure, but return the OperationResult object
            //successfully so that front end can see error and fail gracefully
            $errorMessage = $ex->getMessage();
            error_log('Project update failed: ' . $errorMessage);
            $result->setErrorNumber($ex->getCode());
            $resultMessage = "Project update failed: \n";

            if (strpos($errorMessage, "Foreign key violation")){
                if (strpos($errorMessage, "customerid")) {
                    $resultMessage .= "Assigned customer does not exist.";
                }
                else if (strpos($errorMessage,"areaid")) {
                    $resultMessage .= "Assigned area does not exist.";
                }
            }
            else if (strpos($errorMessage, "Not null violation")) {
                if (strpos($errorMessage,"areaid")) {
                    $resultMessage .= "Area is null. Please choose area.";
                }
            }
            else {
                //if not a predictable error like FK/null violation, just return the native error code and message
                $resultMessage .= $errorMessage;
            }

            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
            $result->setResponseCode(500);
        }

        return $result;
    }

    /** Project creator for PostgreSQL.
     *
     * This function creates a new row for a Project by its {@link ProjectVO}.
     * The internal id of <var>$projectVO</var> will be set after its creation.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to insert on database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     * @throws {@link SQLQueryErrorException}
     */
    public function create(ProjectVO $projectVO) {
        $result = new OperationResult(false);

        $sql = "INSERT INTO project (activation, init, _end, invoice, est_hours,
        areaid, customerid, type, description, moved_hours, sched_type) VALUES(
            :activation, :init, :end, :invoice, :est_hours,
        :areaid, :customerid, :type, :description, :moved_hours, :sched_type)";

        $initDateFormatted = (is_null($projectVO->getInit())) ? null : DBPostgres::formatDate($projectVO->getInit());
        $endDateFormatted = (is_null($projectVO->getEnd())) ? null : DBPostgres::formatDate($projectVO->getEnd());

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":activation", $projectVO->getActivation(), PDO::PARAM_BOOL);
            $statement->bindValue(":init", $initDateFormatted, PDO::PARAM_STR);
            $statement->bindValue(":end", $endDateFormatted, PDO::PARAM_STR);
            $statement->bindValue(":invoice", $projectVO->getInvoice(), PDO::PARAM_STR);
            $statement->bindValue(":est_hours", $projectVO->getEstHours(), PDO::PARAM_STR);
            $statement->bindValue(":areaid", $projectVO->getAreaId(), PDO::PARAM_INT);
            $statement->bindValue(":customerid", $projectVO->getCustomerId(), PDO::PARAM_INT);
            $statement->bindValue(":type", $projectVO->getType(), PDO::PARAM_STR);
            $statement->bindValue(":description", $projectVO->getDescription(), PDO::PARAM_STR);
            $statement->bindValue(":moved_hours", $projectVO->getMovedHours(), PDO::PARAM_STR);
            $statement->bindValue(":sched_type", $projectVO->getSchedType(), PDO::PARAM_STR);
            $statement->execute();

            $projectVO->setId($this->pdo->lastInsertId('project_id_seq'));

            $result->setIsSuccessful(true);
            $result->setMessage('Project created successfully.');
            $result->setResponseCode(201);
        }
        catch (PDOException $ex) {
            //make sure to log the error as a failure, but return the OperationResult object
            //successfully so that front end can see error and fail gracefully
            $errorMessage = $ex->getMessage();
            error_log('Project creation failed: ' . $errorMessage);
            $result->setErrorNumber($ex->getCode());
            $resultMessage = "Project creation failed: \n";

            if(strpos($errorMessage, "Foreign key violation")){
                if(strpos($errorMessage, "customerid")){
                    $resultMessage .= "Customer not yet created. Please create customer first. \n";
                }
                if(strpos($errorMessage,"areaid")){
                    $resultMessage .= "Area not yet created. Please create area first.";
                }
            }
            else if (strpos($errorMessage, "Not null violation")){
                if(strpos($errorMessage,"areaid")){
                    $resultMessage .= "Area is null. Please choose area.";
                }
            }
            else {
                //if not a predictable error like FK/null violation, just return the native error code and message
                $resultMessage .= $errorMessage;
            }

            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
            $result->setResponseCode(500);
        }

        return $result;
    }

    /** Project deleter for PostgreSQL.
     *
     * This function deletes the data of a Project by its {@link ProjectVO}.
     *
     * @param ProjectVO $projectVO the {@link ProjectVO} with the data we want to delete from database.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    public function delete(ProjectVO $projectVO) {
        $result = new OperationResult(false);

        $sql = "DELETE FROM project WHERE id=:id";
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(":id", $projectVO->getId(), PDO::PARAM_INT);
            $statement->execute();

            $result->setIsSuccessful(true);
            $result->setMessage('Project deleted successfully.');
            $result->setResponseCode(200);
        }
        catch (PDOException $ex) {
            $errorMessage = $ex->getMessage();
            error_log('Project deletion failed: ' . $errorMessage);

            $resultMessage = "Project deletion failed: \n";
            if(strpos($errorMessage, "Foreign key violation")) {
                if(strpos($errorMessage, "task")) {
                    $resultMessage .= "The project has assigned tasks.\n";
                }
                else {
                    // catch-all for any foreign key violations that we may add in the future
                    $resultMessage .= "There is data associated to the project.\n";
                }
            }
            else {
                // unpredictable error, just return the native error code and message
                $resultMessage .= $errorMessage;
            }
            $result->setErrorNumber($ex->getCode());
            $result->setMessage($resultMessage);
            $result->setIsSuccessful(false);
            $result->setResponseCode(500);
        }

        return $result;
    }
}
