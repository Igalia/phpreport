<?php

/** File for ProjectsFacade
 *
 *  This file just contains {@link ProjectsFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once('phpreport/model/facade/action/CreateProjectAction.php');
include_once('phpreport/model/facade/action/GetAllProjectsAction.php');
include_once('phpreport/model/facade/action/GetProjectExtraDataAction.php');
include_once('phpreport/model/facade/action/GetProjectAction.php');
include_once('phpreport/model/facade/action/DeleteProjectAction.php');
include_once('phpreport/model/facade/action/UpdateProjectAction.php');
include_once('phpreport/model/facade/action/PartialUpdateProjectAction.php');
include_once('phpreport/model/facade/action/GetProjectsByCustomerUserLoginAction.php');
include_once('phpreport/model/dao/DAOFactory.php');
include_once('phpreport/model/vo/ProjectVO.php');

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

    /** Get Project Extra Data Function
     *
     *  This function is used for retrieving extra data about a Project.
     *
     * @param int $id the database identifier of the Project whose extra data we want to retieve.
     * @return array an array with extra data as associative fields 'total' and 'currentInvoice'.
     */
    static function GetProjectExtraData($projectId) {

    $action = new GetProjectExtraDataAction($projectId);

    return $action->execute();

    }

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

    /** Projects retriever by id (relationship ProjectUser) for PostgreSQL.
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
     * @return array Returns a list of ProjectVO objects satisfying the received criteria.
     */
    static function GetProjectsByCustomerUserLogin($customerId = NULL, $userLogin = NULL, $active = False) {

    if (is_null($customerId) and is_null($userLogin) and !$active)
        $action = new GetAllProjectsAction($active);
    else
        $action = new GetProjectsByCustomerUserLoginAction($customerId, $userLogin, $active);

    return $action->execute();

    }

}

//var_dump(ProjectsFacade::GetProjectsByCustomerUserLogin(10,'jaragunde'));
