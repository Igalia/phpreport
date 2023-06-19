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


/** File for ProjectsFacade
 *
 *  This file just contains {@link ProjectsFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/CreateProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllProjectsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserProjectsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectByDescriptionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/AssignUserToProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeassignUserFromProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');

/** Projects Facade
 *
 *  This Facade contains the functions used in tasks related to Projects.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class ProjectsFacade {

    /** Get Project Function
     *
     *  This function is used for retrieving a Project.
     *
     * @param int $id the database identifier of the Project we want to retieve.
     * @return ProjectVO the Project as a {@link ProjectVO} with its properties set to the values from the row.
     */
    static function GetProject($projectId) {

    $action = new GetProjectAction($projectId);

    return $action->execute();

    }

    /** Get all Projects Function
    *
    * This action is used for retrieving all Projects.
    *
    * @param string $userLogin
    * @param bool $active
    * @param string $order
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
    * @param string $cname string to filter projects by their customer name. NULL
    *        to deactivate filtyering by this field
    * @param boolean $returnExtendedInfo flag to check if the response should include more information
    * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
    * and ordered ascendantly by their database internal identifier.
    */
    static function GetAllProjects($userLogin = NULL, $active = False, $order = 'id', $description = NULL,
        $filterStartDate = NULL, $filterEndDate = NULL, $activation = NULL, $areaId = NULL,
        $type = NULL, $customerId = NULL, $cname = NULL, $returnExtendedInfo = False) {
        $action = new GetAllProjectsAction($userLogin, $active, $order, $description, $filterStartDate,
            $filterEndDate, $activation, $areaId, $type, $customerId, $cname, $returnExtendedInfo);
        return $action->execute();
    }

    /** Get Custom Project Function
     *
     *  This function is used for retrieving a Project with additional data.
     *
     * @param int $id the database identifier of the Project whose Custom Project we want to retieve.
     * @return ProjectVO the Project as a {@link CustomProjectVO} with its properties set to the values from the row
     * and additional data.
     */
    static function GetCustomProject($projectId) {

    $action = new GetCustomProjectAction($projectId);

    return $action->execute();

    }

    /** Create Project Function
     *
     *  This function is used for creating a new Project.
     *
     * @param ProjectVO $project the Project value object we want to create.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     * @throws {@link SQLQueryErrorException}
     */
    static function CreateProject(ProjectVO $project) {

    $action = new CreateProjectAction($project);

    return $action->execute();

    }

    /** Create Projects Function
     *
     *  This function is used for creating an array of new Projects.
     *  If an error occurs, it stops creating.
     *
     * @param array $projects the Project value objects we want to create.
     * @return array OperationResult the araray of results {@link OperationResult} with information about operation status
     * @throws {@link SQLQueryErrorException}
     */
    static function CreateProjects($projects) {
        $operationResults = [];
        foreach ((array) $projects as $project)
            $operationResults[] = ProjectsFacade::CreateProject($project);
        return $operationResults;
    }

    /** Delete Project Function
     *
     *  This function is used for deleting a Project.
     *
     * @param ProjectVO $project the Project value object we want to delete.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    static function DeleteProject(ProjectVO $project) {
        $action = new DeleteProjectAction($project);
        return $action->execute();
    }

    /** Delete Projects Function
     *
     *  This function is used for deleting an array of Projects.
     *  If an error occurs, it stops deleting.
     *
     * @param array $projects the Project value objects we want to delete.
     * @return array OperationResult the array of results {@link OperationResult} with information about operation status.
     */
    static function DeleteProjects($projects) {
        $operationResults = [];
        foreach ((array) $projects as $project)
            $operationResults[] = ProjectsFacade::DeleteProject($project);
        return $operationResults;
    }

    /** Project Users Assigning
     *
     *  This function is used for assigning a User to a Project by their ids.
     *
     * @param int $userId the id of the User we want to assign.
     * @param int $projectId the Project which we want to assign the User to.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function AssignUserToProject($userId, $projectId) {

        $action = new AssignUserToProjectAction($userId, $projectId);

        return $action->execute();

    }

    /** Project Users Deassigning
     *
     *  This function is used for deassigning a User from a Project by their ids.
     *
     * @param int $userId the id of the User we want to deassign.
     * @param int $projectId the UserGroup which we want to deassign the Project from.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function DeassignUserFromProject($userId, $projectId) {

        $action = new DeassignUserFromProjectAction($userId, $projectId);

        return $action->execute();

    }

    /** Projects retriever by User id (relationship ProjectUser) for PostgreSQL.
     *
     * This function retrieves the rows from Project table that are assigned through relationship ProjectUser to the User with
     * the id <var>$userId</var> and creates a {@link ProjectVO} with data from each row.
     *
     * @param int $userId the id of the User whose Projects we want to retrieve.
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetUserProjects($userId) {

        $action = new GetUserProjectsAction($userId);

        return $action->execute();

    }

    /** Users retriever by Project id (relationship ProjectUser) for PostgreSQL.
     *
     * This function retrieves the rows from User table that are assigned through relationship ProjectUser to the
     * Project with the id <var>$projectId</var> and creates a {@link UserVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Users we want to retrieve.
     * @return array an array with value objects {@link UserVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetProjectUsers($projectId) {

        $action = new GetProjectUsersAction($projectId);

        return $action->execute();

    }

    static function GetProjectByDescription(string $description) {

        $action = new GetProjectByDescriptionAction($description);

        return $action->execute();

    }

    /** Update Project Function
     *
     *  This function is used for updating an entire Project object. It updates all the fields of the object.
     *
     * @param ProjectVO $project the Project value object we want to update.
     * @return OperationResult the result {@link OperationResult} with information about operation status
     */
    static function UpdateProject(ProjectVO $project) {
        $action = new UpdateProjectAction($project);
        return $action->execute();
    }

    /** Update Projects Function
     *
     *  This function is used for updating an array of Project objects. It updates all the fields of the object.
     *
     * @param array $projects the Project value objects we want to update.
     * @return array OperationResult the array of results {@link OperationResult} with information about operation status.
     */
    static function UpdateProjects($projects) {
        $operationResults = [];
        foreach ($projects as $project)
            $operationResults[] = ProjectsFacade::UpdateProject($project);
        return $operationResults;
    }
}
