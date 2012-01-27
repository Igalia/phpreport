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

/** URL Header
 *
 * This string contains the common part of the URL, so we don't have
 * have to modify all the permissions if it changes. It's concatted
 * with the permissions (e.g., /phpreport/iterationForm.php).
 */
$urlHeader = "/web";

/** Permissions array
 *
 * It contains a multiple-level array with the groups and the
 * pages/services they are allowed to use. In every element of
 * the array, the key is the name of the group and the content is
 * another array with the urls the group is allowed to open.
 * For example: "clients" => array("/xptracker-summary.php")
 */
$permissions = array(
  "staff" => array("/tasks.php", "/analysistracker-summary.php", "/xptracker-summary.php", "/iterationForm.php", "/moduleForm.php", "/storyForm.php", "/sectionForm.php", "/taskStoryForm.php", "/taskSectionForm.php", "/viewProjectDetails.php", "/viewUsers.php", "/viewUserDetails.php", "/viewWorkingHoursResultsReport.php", "/viewIteration.php", "/viewModule.php", "/viewStory.php", "/viewSection.php", "/viewAreas.php", "/services/createTasksService.php", "/services/deleteTasksService.php", "/services/getCustomerProjectsService.php", "/services/getExtraHoursReportService.php", "/services/getIterationCustomStoriesService.php", "/services/getOpenTaskStoriesService.php", "/services/getPendingHolidayHoursService.php", "/services/getProjectAnalysisTrackerTree.php", "/services/getProjectIterationsService.php", "/services/getProjectTrackerTree.php", "/services/getProjectTtypeReportService.php", "/services/getProjectUserCustomerReportJsonService.php", "/services/getProjectUserCustomerReportService.php", "/services/getProjectUserStoryReportService.php", "/services/getStoryCustomTaskStoriesService.php", "/services/getUserCustomersService.php", "/services/getUserProjectCustomerReportJsonService.php", "/services/getUsersProjectsReportService.php", "/services/getUserTasksService.php", "/services/setTasksJsonService.php", "/services/updateTasksService.php", "/services/updateTaskStoriesService.php", "/services/createTaskStoriesService.php", "/services/deleteTaskStoriesService.php", "/services/updateTaskSectionsService.php", "/services/createTaskSectionsService.php", "/services/deleteTaskSectionsService.php", "/services/getSectionCustomTaskSectionsService.php", "/services/getAllUsersService.php", "/services/getUserHourCostHistoriesService.php", "/services/getUserAreaHistoriesService.php", "/services/getUserCityHistoriesService.php", "/services/getUserJourneyHistoriesService.php", "/viewProjects.php", "/services/getAllProjectsService.php", "/services/getTodayAreaUsersService.php", "/services/getProjectUsersService.php", "/services/getAllCustomersService.php", "/services/getProjectCustomersService.php", "/services/getAllCustomProjectsService.php", "/services/getFilteredCustomProjectsService.php", "/projectsEvaluation.php", "/services/getProjectCustomerReportJsonService.php", "/projectsSummary.php", "/services/getProjectUserReportJsonService.php", "/services/getPersonalSummaryByDateService.php", "/services/getProjectUserStoryReportJsonService.php", '/services/getUserStoryReportJsonService.php', '/usersEvaluation.php', '/services/getProjectService.php', '/services/getAllCitiesService.php'),
  "admin" => array("/services/createProjectsService.php", "/services/deleteProjectsService.php", "/services/updateProjectsService.php", "/services/updateUsersService.php", "/services/createUsersService.php", "/services/deleteUsersService.php","/services/createJourneyHistoriesService.php", "/services/createHourCostHistoriesService.php", "/services/createAreaHistoriesService.php", "/services/createCityHistoriesService.php", "/services/updateJourneyHistoriesService.php", "/services/updateHourCostHistoriesService.php", "/services/updateAreaHistoriesService.php", "/services/updateCityHistoriesService.php", "/services/deleteJourneyHistoriesService.php", "/services/deleteHourCostHistoriesService.php", "/services/deleteAreaHistoriesService.php", "/services/deleteCityHistoriesService.php","/services/assignUsersToProjectService.php", "/services/deassignUsersFromProjectService.php","/viewCustomers.php", "/services/createCustomersService.php", "/services/updateCustomersService.php", "/services/deleteCustomersService.php", "/services/getAllSectorsService.php", "/services/updateSectorsService.php", "/services/createSectorsService.php", "/services/deleteSectorsService.php", "/services/createAreasService.php", "/services/deleteAreasService.php", "/services/getAllAreasService.php", "/services/updateAreasService.php","/services/assignCustomersToProjectService.php", "/services/deassignCustomersFromProjectService.php","/calendarManagement.php", "/services/getCommonEventsByCityIdJsonService.php", "/services/getCommonEventsByCityIdService.php", "/services/createCommonEventsService.php", "/services/deleteCommonEventsService.php", '/cityManagement.php', '/services/getAllCitiesService.php', '/services/createCitiesService.php', '/services/updateCitiesService.php')
);

/** Admin permissions array
 *
 * It contains a multiple-level array with the groups and the
 * pages they are allowed to act on as admin. In every element of
 * the array, the key is the name of the group and the content is
 * another array with the urls the group is allowed to open.
 * For example: "clients" => array("/xptracker-summary.php")
 */
$adminPermissions = array(
  "admin" => array("/tasks.php", "/analysistracker-summary.php", "/xptracker-summary.php", "/iterationForm.php", "/moduleForm.php", "/storyForm.php", "/sectionForm.php", "/taskStoryForm.php", "/taskSectionForm.php", "/viewProjectDetails.php", "/viewUsers.php", "/viewUserDetails.php", "/viewWorkingHoursResultsReport.php", "/viewIteration.php", "/viewModule.php", "/viewStory.php", "/viewSection.php", "/services/createProjectsService.php", "/services/createTasksService.php", "/services/deleteProjectsService.php", "/services/deleteTasksService.php", "/services/getCustomerProjectsService.php", "/services/getExtraHoursReportService.php", "/services/getIterationCustomStoriesService.php", "/services/getOpenTaskStoriesService.php", "/services/getPendingHolidayHoursService.php", "/services/getProjectAnalysisTrackerTree.php", "/services/getProjectIterationsService.php", "/services/getProjectTrackerTree.php", "/services/getProjectTtypeReportService.php", "/services/getProjectUserCustomerReportJsonService.php", "/services/getProjectUserCustomerReportService.php", "/services/getProjectUserStoryReportService.php", "/services/getStoryCustomTaskStoriesService.php", "/services/getUserCustomersService.php", "/services/getUserProjectCustomerReportJsonService.php", "/services/getUsersProjectsReportService.php", "/services/getUserTasksService.php", "/services/setTasksJsonService.php", "/services/updateProjectsService.php", "/services/updateTasksService.php", "/services/updateTaskStoriesService.php", "/services/createTaskStoriesService.php", "/services/deleteTaskStoriesService.php", "/services/updateTaskSectionsService.php", "/services/createTaskSectionsService.php", "/services/deleteTaskSectionsService.php", "/services/getSectionCustomTaskSectionsService.php", "/services/getAllUsersService.php", "/services/updateUsersService.php", "/services/createUsersService.php", "/services/deleteUsersService.php", "/services/getUserHourCostHistoriesService.php", "/services/getUserAreaHistoriesService.php", "/services/getUserCityHistoriesService.php", "/services/getUserJourneyHistoriesService.php", "/services/createJourneyHistoriesService.php", "/services/createHourCostHistoriesService.php", "/services/createAreaHistoriesService.php", "/services/createCityHistoriesService.php", "/services/updateJourneyHistoriesService.php", "/services/updateHourCostHistoriesService.php", "/services/updateAreaHistoriesService.php", "/services/updateCityHistoriesService.php", "/services/deleteJourneyHistoriesService.php", "/services/deleteHourCostHistoriesService.php", "/services/deleteAreaHistoriesService.php", "/services/deleteCityHistoriesService.php", "/viewProjects.php"),
);
