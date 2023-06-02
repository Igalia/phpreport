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
    'staff' => array(
        //tasks
        '/tasks.php', '/services/getUserTasksService.php',
        '/services/getPersonalSummaryByDateService.php',
        '/services/getUserCustomersService.php', '/services/getOpenTaskStoriesService.php',
        '/services/createTasksService.php', '/services/deleteTasksService.php',
        '/services/updateTasksService.php',
        '/services/getTasksFiltered.php', '/userTasksReport.php',
        '/services/getTaskTypes.php',
        //holidays management
        '/services/getHolidays.php',
        '/services/updateHolidays.php',
        '/services/syncCalendar.php',
        '/holidayManagement.php',
        '/holidaySummary.php',
        '/services/getHolidaySummary.php',
        //templates
        '/services/createTemplatesService.php', '/services/getUserTemplatesService.php',
        '/services/deleteTemplatesService.php',
        //reports
        '/projectDetailsReport.php', '/viewUserDetails.php', '/projectDetails.php', '/viewWorkingHoursResultsReport.php',
        '/services/getExtraHoursReportService.php', '/services/getPendingHolidayHoursService.php',
        '/services/getProjectTtypeReportService.php', '/services/getProjectUserCustomerReportJsonService.php',
        '/services/getProjectUserCustomerReportService.php', '/services/getProjectUserStoryReportService.php',
        '/services/getUserProjectCustomerReportJsonService.php', '/services/getUsersProjectsReportService.php',
        '/services/getProjectUserStoryReportJsonService.php',
        '/services/getProjectUserWeeklyHoursReportJsonService.php',
        //common services
        '/services/getAllUsersService.php', '/services/getAllCustomersService.php',
        '/services/getAllCitiesService.php', '/services/getProjectService.php', '/services/getProjectsService.php',
        '/services/getAllSectorsService.php', '/services/getAllAreasService.php',
        '/services/getAllExtraHourVOsService.php', '/services/getAllCitiesService.php',
        '/services/getUserGoalsService.php', '/services/createUserGoalsService.php', '/services/updateUserGoalsService.php',
        '/services/deleteUserGoalsService.php',
        '/fastapiTest.php',
        //user management screen (read only)
        '/viewUsers.php',
        '/services/getUserHourCostHistoriesService.php', '/services/getUserAreaHistoriesService.php',
        '/services/getUserCityHistoriesService.php', '/services/getUserJourneyHistoriesService.php',
        '/services/getTodayAreaUsersService.php', '/services/getProjectUsersService.php'
    ),
    'manager' => array(
        '/projectsEvaluation.php', '/usersEvaluation.php', '/projectsSummary.php', '/services/getUserStoryReportJsonService.php',
        '/services/getProjectCustomerReportJsonService.php', '/services/getProjectUserReportJsonService.php',
    ),
    'admin' => array(
        //projects management
        '/services/createProjectsService.php', '/services/deleteProjectsService.php',
        '/services/updateProjectsService.php', '/projectManagement.php',
        //project attributes management
        '/services/assignUsersToProjectService.php', '/services/deassignUsersFromProjectService.php',
        //users management
        '/services/updateUsersService.php', '/services/createUsersService.php',
        '/services/deleteUsersService.php',
        //user attributes management
        '/services/createJourneyHistoriesService.php', '/services/createHourCostHistoriesService.php',
        '/services/createAreaHistoriesService.php', '/services/createCityHistoriesService.php',
        '/services/updateJourneyHistoriesService.php', '/services/updateHourCostHistoriesService.php',
        '/services/updateAreaHistoriesService.php', '/services/updateCityHistoriesService.php',
        '/services/deleteJourneyHistoriesService.php', '/services/deleteHourCostHistoriesService.php',
        '/services/deleteAreaHistoriesService.php', '/services/deleteCityHistoriesService.php',
        //customers management
        '/customerManagement.php', '/services/createCustomersService.php',
        '/services/updateCustomersService.php', '/services/deleteCustomersService.php',
        //customer attributes management
        '/services/updateSectorsService.php',
        '/services/createSectorsService.php', '/services/deleteSectorsService.php',
        //areas management
        '/services/createAreasService.php', '/services/deleteAreasService.php',
        '/services/updateAreasService.php', '/areaManagement.php',
        //calendar management
        '/calendarManagement.php', '/services/getCommonEventsByCityIdJsonService.php',
        '/services/getCommonEventsByCityIdService.php', '/services/createCommonEventsService.php',
        '/services/deleteCommonEventsService.php',
        //city management
        '/cityManagement.php',
        '/services/createCitiesService.php', '/services/updateCitiesService.php',
        '/services/deleteCitiesService.php',
        //hour compensations management
        '/services/createExtraHourVOsService.php',
        '/services/updateExtraHourVOsService.php', '/services/deleteExtraHourVOsService.php',
        '/hourCompensationManagement.php',
        //system settings
        '/settings.php',
        //API test
        '/APITest.php',
        //Vacations management
        '/services/updateLongLeaves.php',
        '/longLeaves.php',
    )
);

/** Extra permissions array
 *
 * It contains a multiple-level array with the groups and the
 * pages they are allowed to act on as admin. In every element of
 * the array, the key is the name of the group and the content is
 * another array with the urls the group is allowed to open.
 * For example: "clients" => array("/xptracker-summary.php")
 */
$extraPermissions = array(
    "admin" => array(
        //users management
        "/viewUsers.php",
    ),
    'manager' => array(
        //user tasks report: enable user field to check any user
        "/userTasksReport.php", "/services/getTasksFiltered.php",
        //project details reports: check any project
        '/projectDetailsReport.php', '/projectDetails.php',
        '/services/getProjectUserCustomerReportJsonService.php',
        //user details report: check any user
        '/viewUserDetails.php',
        '/services/getUserProjectCustomerReportJsonService.php'
    )
);
