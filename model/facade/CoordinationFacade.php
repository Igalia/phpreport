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


/** File for CoordinationFacade
 *
 *  This file just contains {@link CoordinationFacade}.
 *
 * @filesource
 * @package PhpReport
 * @subpackage facade
 * @author Jorge López Fernández <jlopez@igalia.com>
 */

include_once(PHPREPORT_ROOT . '/model/facade/action/CreateProjectScheduleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteProjectScheduleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateProjectScheduleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateIterationAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteIterationAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateIterationAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateTaskStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteTaskStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateTaskStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateModuleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteModuleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateModuleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/CreateTaskSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/DeleteTaskSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/UpdateTaskSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectIterationsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetIterationAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetIterationStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetIterationCustomStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryTaskSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryCustomTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetSectionCustomTaskSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetModuleCustomSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetTaskStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomTaskStoryAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetOpenTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetTaskStoryTasksAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetStoryTasksAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetProjectModulesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetModuleAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetModuleSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetSectionTaskSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetTaskSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetCustomTaskSectionAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetOpenTaskSectionsAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetSectionTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/facade/action/GetTaskSectionTaskStoriesAction.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectScheduleVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/IterationVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/StoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/TaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomTaskStoryVO.php');
include_once(PHPREPORT_ROOT . '/model/vo/CustomStoryVO.php');

/** Coordination Facade
 *
 *  This Facade contains the functions used in Projects Coordination tasks.
 *
 * @package PhpReport
 * @subpackage facade
 * @todo create the retrieval functions.
 * @author Jorge López Fernández <jlopez@igalia.com>
 */
abstract class CoordinationFacade {

    /** Create Project Schedule Function
     *
     *  This function is used for creating a new Project Schedule.
     *
     * @param ProjectScheduleVO $projectSchedule the Project Schedule value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateProjectSchedule(ProjectScheduleVO $projectSchedule) {

    $action = new CreateProjectScheduleAction($projectSchedule);

    return $action->execute();

    }

    /** Delete Project Schedule Function
     *
     *  This function is used for deleting a Project Schedule.
     *
     * @param ProjectScheduleVO $projectSchedule the Project Schedule value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteProjectSchedule(ProjectScheduleVO $projectSchedule) {

    $action = new DeleteProjectScheduleAction($projectSchedule);

    return $action->execute();

    }

    /** Update Project Schedule Function
     *
     *  This function is used for updating a Project Schedule.
     *
     * @param ProjectScheduleVO $projectSchedule the Project Schedule value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateProjectSchedule(ProjectScheduleVO $projectSchedule) {

    $action = new UpdateProjectScheduleAction($projectSchedule);

    return $action->execute();

    }

    /** Get Iteration Function
     *
     *  This action is used for retrieving an Iteration.
     *
     * @param int $id the database identifier of the Iteration we want to retieve.
     * @return IterationVO the Iteration as a {@link IterationVO} with its properties set to the values from the row.
     */
    static function GetIteration($iterationId) {

    $action = new GetIterationAction($iterationId);

    return $action->execute();

    }

    /** Create Iteration Function
     *
     *  This function is used for creating a new Iteration.
     *
     * @param IterationVO $iteration the Iteration value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateIteration(IterationVO $iteration) {

    $action = new CreateIterationAction($iteration);

    return $action->execute();

    }

    /** Delete Iteration Function
     *
     *  This function is used for deleting an Iteration.
     *
     * @param IterationVO $iteration the Iteration value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteIteration(IterationVO $iteration) {

    $action = new DeleteIterationAction($iteration);

    return $action->execute();

    }

    /** Update Iteration Function
     *
     *  This function is used for updating a Iteration.
     *
     * @param IterationVO $iteration the Iteration value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateIteration(IterationVO $iteration) {

    $action = new UpdateIterationAction($iteration);

    return $action->execute();

    }

    /** Get Project Iterations Function
     *
     *  This function is used for retrieving all Iterations related to a Project.
     *
     * @param int $projectId the id of the Project whose Iterations we want to retieve.
     * @return array an array with value objects {@link IterationVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetProjectIterations($projectId) {

    $action = new GetProjectIterationsAction($projectId);

    return $action->execute();

    }

    /** Get Story Function
     *
     *  This action is used for retrieving a Story.
     *
     * @param int $storyId the database identifier of the Story we want to retieve.
     * @return StoryVO the Story as a {@link StoryVO} with its properties set to the values from the row.
     */
    static function GetStory($storyId) {

    $action = new GetStoryAction($storyId);

    return $action->execute();

    }

    /** Create Story Function
     *
     *  This function is used for creating a new Story.
     *
     * @param StoryVO $story the Story value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateStory(StoryVO $story) {

    $action = new CreateStoryAction($story);

    return $action->execute();

    }

    /** Delete Story Function
     *
     *  This function is used for deleting a Story.
     *
     * @param StoryVO $story the Story value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteStory(StoryVO $story) {

    $action = new DeleteStoryAction($story);

    return $action->execute();

    }

    /** Update Story Function
     *
     *  This function is used for updating a Story.
     *
     * @param StoryVO $story the Story value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateStory(StoryVO $story) {

    $action = new UpdateStoryAction($story);

    return $action->execute();

    }

    /** Get Iteration Stories Function
     *
     *  This function is used for retrieving all Stories related to an Iteration.
     *
     * @param int $projectId the id of the Iteration whose Stories we want to retieve.
     * @return array an array with value objects {@link StoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetIterationStories($iterationId) {

    $action = new GetIterationStoriesAction($iterationId);

    return $action->execute();

    }

    /** Get Iteration custom Stories Function
     *
     *  This function is used for retrieving all custom Stories related to an Iteration.
     *
     * @param int $iterationId the id of the Iteration whose Custom Stories we want to retieve.
     * @return array an array with value objects {@link CustomStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetIterationCustomStories($iterationId) {

    $action = new GetIterationCustomStoriesAction($iterationId);

    return $action->execute();

    }

    /** Get custom Story Function
     *
     *  This function is used for retrieving a custom Story by it's id.
     *
     * @param int $storyId the id of the Custom Story we want to retieve.
     * @return CustomStoryVO a custom object {@link CustomStoryVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    static function GetCustomStory($storyId) {

    $action = new GetCustomStoryAction($storyId);

    return $action->execute();

    }

    /** Get custom Section Function
     *
     *  This function is used for retrieving a custom Section by it's id.
     *
     * @param int $sectionId the id of the Custom Section we want to retieve.
     * @return CustomSectionVO a custom object {@link CustomSectionVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    static function GetCustomSection($sectionId) {

    $action = new GetCustomSectionAction($sectionId);

    return $action->execute();

    }

    /** Get Task Story Function
     *
     *  This action is used for retrieving a Task Story.
     *
     * @param int $taskStoryId the database identifier of the Task Story we want to retieve.
     * @return TaskStoryVO the Task Story as a {@link TaskStoryVO} with its properties set to the values from the row.
     */
    static function GetTaskStory($taskStoryId) {

    $action = new GetTaskStoryAction($taskStoryId);

    return $action->execute();

    }

    /** Get custom Task Story Function
     *
     *  This function is used for retrieving a custom Task Story by it's id.
     *
     * @param int $taskStoryId the id of the Custom Task Story we want to retieve.
     * @return CustomTaskStoryVO a custom object {@link CustomTaskStoryVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    static function GetCustomTaskStory($taskStoryId) {

    $action = new GetCustomTaskStoryAction($taskStoryId);

    return $action->execute();

    }

    /** Create Task Story Function
     *
     *  This function is used for creating a new Task Story.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateTaskStory(TaskStoryVO $taskStory) {

    $action = new CreateTaskStoryAction($taskStory);

    return $action->execute();

    }

    /** Delete Task Story Function
     *
     *  This function is used for deleting a Task Story.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteTaskStory(TaskStoryVO $taskStory) {

    $action = new DeleteTaskStoryAction($taskStory);

    return $action->execute();

    }

    /** Update Task Story Function
     *
     *  This function is used for updating a Task Story.
     *
     * @param TaskStoryVO $taskStory the Task Story value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateTaskStory(TaskStoryVO $taskStory) {

    $action = new UpdateTaskStoryAction($taskStory);

    return $action->execute();

    }

    /** Get Story Tasks Function
     *
     *  This function is used for retrieving all Tasks related to a Story through its Task Stories.
     *
     * @param int $storyId the id of the Story whose Tasks we want to retrieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetStoryTasks($storyId) {

    $action = new GetStoryTasksAction($storyId);

    return $action->execute();

    }

    /** Get Story Task Stories Function
     *
     *  This function is used for retrieving all Task Stories related to a Story.
     *
     * @param int $storyId the id of the Story whose Task Stories we want to retieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetStoryTaskStories($storyId) {

    $action = new GetStoryTaskStoriesAction($storyId);

    return $action->execute();

    }

    /** Get Story Task Stories Function
     *
     *  This action is used for retrieving all Task Sections related to a Story through its Project.
     *
     * @param int $storyId the id of the Story whose Task Sections we want to retieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetStoryTaskSections($storyId) {

    $action = new GetStoryTaskSectionsAction($storyId);

    return $action->execute();

    }

    /** Get Story custom Task Stories Function
     *
     *  This function is used for retrieving all custom Task Stories related to a Story.
     *
     * @param int $storyId the id of the Story whose custom Task Stories we want to retieve.
     * @return array an array with value objects {@link CustomTaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetStoryCustomTaskStories($storyId) {

    $action = new GetStoryCustomTaskStoriesAction($storyId);

    return $action->execute();

    }

    /** Open TaskStories retrieving function.
     *
     * This function retrieves all Task Stories that don't have an ending date assigned.
     * We can pass optional parameters for filtering by User, <var>$userId</var>,
     * and by Project, <var>$projectId</var>.
     *
     * @param int $userId optional parameter for filtering by User.
     * @param int $projectId optional parameter for filtering by Project.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetOpenTaskStories($userId = NULL, $projectId = NULL) {

    $action = new GetOpenTaskStoriesAction($userId, $projectId);

    return $action->execute();

    }

    /** Get Task Story Tasks Function
     *
     *  This function is used for retrieving all Tasks related to a Task Story.
     *
     * @param int $taskStoryId the id of the Task Story whose Tasks we want to retieve.
     * @return array an array with value objects {@link TaskVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetTaskStoryTasks($taskStoryId) {

    $action = new GetTaskStoryTasksAction($taskStoryId);

    return $action->execute();

    }

    /** Get Module Function
     *
     *  This action is used for retrieving a Module.
     *
     * @param int $moduleId the database identifier of the Module we want to retieve.
     * @return ModuleVO the Module as a {@link ModuleVO} with its properties set to the values from the row.
     */
    static function GetModule($moduleId) {

    $action = new GetModuleAction($moduleId);

    return $action->execute();

    }

    /** Create Module Function
     *
     *  This function is used for creating a new Module.
     *
     * @param ModuleVO $module the Module value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateModule(ModuleVO $module) {

    $action = new CreateModuleAction($module);

    return $action->execute();

    }

    /** Delete Module Function
     *
     *  This function is used for deleting a Module.
     *
     * @param ModuleVO $module the Module value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteModule(ModuleVO $module) {

    $action = new DeleteModuleAction($module);

    return $action->execute();

    }

    /** Update Module Function
     *
     *  This function is used for updating a Module.
     *
     * @param ModuleVO $module the Module value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateModule(ModuleVO $module) {

    $action = new UpdateModuleAction($module);

    return $action->execute();

    }

    /** Get Project Modules Function
     *
     *  This function is used for retrieving all Modules related to a Project.
     *
     * @param int $projectId the id of the Project whose Modules we want to retieve.
     * @return array an array with value objects {@link ModuleVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetProjectModules($projectId) {

    $action = new GetProjectModulesAction($projectId);

    return $action->execute();

    }

    /** Get Module custom Sections Function
     *
     *  This function is used for retrieving all custom Sections related to a Module.
     *
     * @param int $moduleId the id of the Module whose custom Sections we want to retieve.
     * @return array an array with value objects {@link CustomSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetModuleCustomSections($moduleId) {

    $action = new GetModuleCustomSectionsAction($moduleId);

    return $action->execute();

    }

    /** Get Section Function
     *
     *  This action is used for retrieving a Section.
     *
     * @param int $sectionId the database identifier of the Section we want to retieve.
     * @return SectionVO the Section as a {@link SectionVO} with its properties set to the values from the row.
     */
    static function GetSection($sectionId) {

    $action = new GetSectionAction($sectionId);

    return $action->execute();

    }

    /** Get custom Task Section Function
     *
     *  This function is used for retrieving a custom Task Section by it's id.
     *
     * @param int $taskSectionId the id of the Custom Task Section we want to retieve.
     * @return CustomTaskSectionVO a custom object {@link CustomTaskSectionVO} with it's properties set to the values
     * from the rows, and with others derived.
     */
    static function GetCustomTaskSection($taskSectionId) {

    $action = new GetCustomTaskSectionAction($taskSectionId);

    return $action->execute();

    }

    /** Create Section Function
     *
     *  This function is used for creating a new Section.
     *
     * @param SectionVO $section the Section value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateSection(SectionVO $section) {

    $action = new CreateSectionAction($section);

    return $action->execute();

    }

    /** Delete Section Function
     *
     *  This function is used for deleting a Section.
     *
     * @param SectionVO $section the Section value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteSection(SectionVO $section) {

    $action = new DeleteSectionAction($section);

    return $action->execute();

    }

    /** Update Section Function
     *
     *  This function is used for updating a Section.
     *
     * @param SectionVO $section the Section value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateSection(SectionVO $section) {

    $action = new UpdateSectionAction($section);

    return $action->execute();

    }

    /** Get Module Sections Function
     *
     *  This function is used for retrieving all Sections related to an Module.
     *
     * @param int $projectId the id of the Module whose Sections we want to retieve.
     * @return array an array with value objects {@link SectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetModuleSections($moduleId) {

    $action = new GetModuleSectionsAction($moduleId);

    return $action->execute();

    }

    /** Get Section custom Task Sections Function
     *
     *  This function is used for retrieving all custom Task Sections related to a Section.
     *
     * @param int $sectionId the id of the Section whose custom Task Sections we want to retieve.
     * @return array an array with value objects {@link CustomTaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetSectionCustomTaskSections($sectionId) {

    $action = new GetSectionCustomTaskSectionsAction($sectionId);

    return $action->execute();

    }

    /** Get Task Section Function
     *
     *  This action is used for retrieving a Task Section.
     *
     * @param int $taskSectionId the database identifier of the Task Section we want to retieve.
     * @return TaskSectionVO the Task Section as a {@link TaskSectionVO} with its properties set to the values from the row.
     */
    static function GetTaskSection($taskSectionId) {

    $action = new GetTaskSectionAction($taskSectionId);

    return $action->execute();

    }

    /** Create Task Section Function
     *
     *  This function is used for creating a new Task Section.
     *
     * @param TaskSectionVO $taskSection the Task Section value object we want to create.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function CreateTaskSection(TaskSectionVO $taskSection) {

    $action = new CreateTaskSectionAction($taskSection);

    return $action->execute();

    }

    /** Delete Task Section Function
     *
     *  This function is used for deleting a Task Section.
     *
     * @param TaskSectionVO $taskSection the Task Section value object we want to delete.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}
     */
    static function DeleteTaskSection(TaskSectionVO $taskSection) {

    $action = new DeleteTaskSectionAction($taskSection);

    return $action->execute();

    }

    /** Update Task Section Function
     *
     *  This function is used for updating a Task Section.
     *
     * @param TaskSectionVO $taskSection the Task Section value object we want to update.
     * @return int it just indicates if there was any error (<i>-1</i>) or not (<i>0</i>).
     * @throws {@link SQLQueryErrorException}, {@link SQLUniqueViolationException}
     */
    static function UpdateTaskSection(TaskSectionVO $taskSection) {

    $action = new UpdateTaskSectionAction($taskSection);

    return $action->execute();

    }

    /** Get Section Task Stories Function
     *
     *  This function is used for retrieving all Task Stories related to a Section through its Task Sections.
     *
     * @param int $sectionId the id of the Section whose Task Stories we want to retrieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetSectionTaskStories($sectionId) {

    $action = new GetSectionTaskStoriesAction($sectionId);

    return $action->execute();

    }

    /** Get Section Task Sections Function
     *
     *  This function is used for retrieving all Task Sections related to a Section.
     *
     * @param int $sectionId the id of the Section whose Task Sections we want to retieve.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetSectionTaskSections($sectionId) {

    $action = new GetSectionTaskSectionsAction($sectionId);

    return $action->execute();

    }

    /** Open Task Sections retrieving function.
     *
     * This function retrieves all Task Sections that don't have an ending date assigned.
     * We can pass optional parameters for filtering by User, <var>$userId</var>,
     * and by Project, <var>$projectId</var>.
     *
     * @param int $userId optional parameter for filtering by User.
     * @param int $projectId optional parameter for filtering by Project.
     * @return array an array with value objects {@link TaskSectionVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     * @throws {@link SQLQueryErrorException}
     */
    static function GetOpenTaskSections($userId = NULL, $projectId = NULL) {

    $action = new GetOpenTaskSectionsAction($userId, $projectId);

    return $action->execute();

    }

    /** Get Task Section Task Stories Function
     *
     *  This function is used for retrieving all Task Stories related to a Task Section.
     *
     * @param int $taskSectionId the id of the Task Section whose Task Stories we want to retieve.
     * @return array an array with value objects {@link TaskStoryVO} with their properties set to the values from the rows
     * and ordered ascendantly by their database internal identifier.
     */
    static function GetTaskSectionTaskStories($taskSectionId) {

    $action = new GetTaskSectionTaskStoriesAction($taskSectionId);

    return $action->execute();

    }

}

/*//Test code

var_dump(CoordinationFacade::GetStoryTasks(3));

$dao = DAOFactory::getStoryDAO();

$story = $dao->getById(5);

var_dump(CoordinationFacade::GetStoryCustomTaskStories(3));

$taskstoryvo = new TaskStoryVO;

$taskstoryvo->setName("Bring the crates");
$taskstoryvo->setRisk(2);
$taskstoryvo->setEstHours(10);
$taskstoryvo->setToDo(3);
$taskstoryvo->setInit(date_create("2009-10-02"));
$taskstoryvo->setEnd(date_create("2009-10-12"));
$taskstoryvo->setEstEnd(date_create("2009-10-22"));
$taskstoryvo->setUserId(2);
$taskstoryvo->setStoryId(2);


CoordinationFacade::CreateTaskStory($taskstoryvo);

var_dump(CoordinationFacade::GetOpenTaskStories(35, 1));

var_dump(CoordinationFacade::GetCustomStory(3));

var_dump(CoordinationFacade::GetStoryTaskSections(3));

var_dump(CoordinationFacade::GetSectionCustomTaskSections(1));*/
