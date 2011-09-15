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
include_once(PHPREPORT_ROOT . '/model/facade/action/GetAllCustomProjectsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetFilteredCustomProjectsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetUserProjectsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectUsersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectCustomersAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/AssignUserToProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeassignUserFromProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/AssignCustomerToProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeassignCustomerFromProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/PartialUpdateProjectAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectsByCustomerUserLoginAction.php');
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
     *  This action is used for retrieving all Projects.
     *
     * @return array an array with value objects {@link ProjectVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetAllProjects() {

        $action = new GetAllProjectsAction();

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

     /** Get all Custom Projects Function
     *
     *  This action is used for retrieving all Projects with additional data.
     *
     * @return array an array with value objects {@link CustomProjectVO} with their properties set to the values from the rows
     * and with additional data, and ordered ascendantly by their database internal identifier.
     */
    static function GetAllCustomProjects() {

        $action = new GetAllCustomProjectsAction();

        return $action->execute();

    }

    /** GetAllCustomProjectsAction constructor.
     *
     * This is just the constructor of this action.
     *
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
     */
    static function GetFilteredCustomProjects($description = NULL,
            $filterStartDate = NULL, $filterEndDate = NULL, $activation = NULL,
            $areaId = NULL, $type = NULL) {

        $action = new GetFilteredCustomProjectsAction($description,
            $filterStartDate, $filterEndDate, $activation, $areaId, $type);

        return $action->execute();
    }

    /** Create Project Function
     *
     *  This function is used for creating a new Project.
     *
     * @param ProjectVO $project the Project value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
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
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function CreateProjects($projects) {

    foreach((array)$projects as $project)
        if ((ProjectsFacade::CreateProject($project)) == -1)
            return -1;

    return 0;

    }

    /** Delete Project Function
     *
     *  This function is used for deleting a Project.
     *
     * @param ProjectVO $project the Project value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
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
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteProjects($projects) {

    foreach((array)$projects as $project)
        if ((ProjectsFacade::DeleteProject($project)) == -1)
            return -1;

    return 0;

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
    public function GetUserProjects($userId) {

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
    public function GetProjectUsers($projectId) {

    $action = new GetProjectUsersAction($projectId);

    return $action->execute();

    }

    /** Project Customer Assigning
     *
     *  This function is used for assigning a Customer to a Project by their ids.
     *
     * @param int $customerId the id of the Customer we want to assign.
     * @param int $projectId the Project which we want to assign the User to.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function AssignCustomerToProject($customerId, $projectId) {

        $action = new AssignCustomerToProjectAction($customerId, $projectId);

        return $action->execute();

    }

    /** Project Customer Deassigning
     *
     *  This function is used for deassigning a Customer from a Project by their ids.
     *
     * @param int $customerId the id of the Customer we want to deassign.
     * @param int $projectId the UserGroup which we want to deassign the Project from.
     *
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function DeassignCustomerFromProject($customerId, $projectId) {

        $action = new DeassignCustomerFromProjectAction($customerId, $projectId);

        return $action->execute();

    }

    /** Customers retriever by Project id for PostgreSQL.
     *
     * This function retrieves the rows from Customer table that are assigned through relationship Requests to the
     * Project with the id <var>$projectId</var> and creates a {@link CustomerVO} with data from each row.
     *
     * @param int $projectId the id of the Project whose Customers we want to retrieve.
     * @return array an array with value objects {@link CustomerVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    public function GetProjectCustomers($projectId) {

    $action = new GetProjectCustomersAction($projectId);

    return $action->execute();

    }

    /** Update Project Function
     *
     *  This function is used for updating a Project.
     *
     * @param ProjectVO $project the Project value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function UpdateProject(ProjectVO $project) {

    $action = new UpdateProjectAction($project);

    return $action->execute();

    }

    /** Partial Update Project Function
     *
     *  This function is used for partially updating a Project.
     *
     * @param ProjectVO $task the Project value object we want to update.
     * @param array $update the updating flags of the Project VO.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function PartialUpdateProject(ProjectVO $project, $update) {

    $action = new PartialUpdateProjectAction($project, $update);

    return $action->execute();

    }

    /** Partial Update Projects Function
     *
     *  This function is used for partially updating an array of Projects.
     *  If an error occurs, it stops updating.
     *
     * @param array $projects the Project value objects we want to update.
     * @param array $updates the updating flag arrays of the Project VOs.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function PartialUpdateProjects($projects, $updates) {

    foreach((array)$projects as $i=>$project)
        if ((ProjectsFacade::PartialUpdateProject($project, $updates[$i])) == -1)
            return -1;

    return 0;

    }

    /** GetProjectsByCustomerUserLogin Function
     *
     *  Retrieves a list of projects using the attributes customer, user and activation as filters.
     *
     * @param int $customerID If not null, the list of projects will contain those related with this customer.
     * @param string $userLogin If not null, the list of projects will contain those the user is assigned to.
     * @param bool $active If true, only active projects will be listed. Otherwise, but inactive and active will appear.
     * @param string $order optional parameter for sorting value objects in a specific way (by default, by their internal id).
     * @return array Returns a list of ProjectVO objects satisfying the received criteria.
     */
    static function GetProjectsByCustomerUserLogin($customerId = NULL, $userLogin = NULL, $active = False, $order = 'id') {
        if (is_null($customerId) and is_null($userLogin) and !$active)
          $action = new GetAllProjectsAction($active, $order);
        else
          $action = new GetProjectsByCustomerUserLoginAction($customerId, $userLogin, $active, $order);

        return $action->execute();
    }

}

//var_dump(ProjectsFacade::GetProjectUsers(4));
