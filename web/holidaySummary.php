<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* Check authentication and authorization */
$sid = $_GET["sid"] ?? NULL;
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Vacation Management");
include_once("include/header.php");

?>

<link rel="stylesheet" type="text/css" href="include/report.css" />

<script src="vuejs/vue.min.js"></script>

<div id="holidaySummaryReport">
    <div v-if="isLoading" class="loaderContainer">
        <div class="loader"></div>
    </div>
    <section v-if="!isLoading" class="container">
        <div class="filters submenu">
            <div class="projectFilter">
                <span>Project</span>
                <div>
                    <input class="autocompleteSearchInput" type="text" v-model="searchProject" :disabled="isLoadingProjects" :placeholder="isLoadingProjects ? 'Loading projects': 'Filter by project'" @focus="showOptions" @keyup="filterProject" @focusout="hideOptions" @keyup.13="onSelectProject(activeProject)" @keyup.38="prevProject" @keyup.40="nextProject" />
                    <ul :class="{ 'hidden': !autocompleteIsActive, 'autocomplete': true}" id="projectsDropdown">
                        <li v-for="(project, index) in projectsList" class="autocompleteItem" v-on:click="onSelectProject(index)">
                            <button :class="{ 'active': index == activeProject, 'autocompleteItemBtn': true}">{{ project.name }}</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="content">
            <h1>Summary for {{year}}</h1>
        </div>
        <table class="report">
            <thead slot="head">
                <th>User</th>
                <th>Last Update</th>
                <th>Area</th>
                <th>Hours/day</th>
                <th>Available (hours)</th>
                <th>Used (hours)</th>
                <th>Scheduled (hours)</th>
                <th>Pending (hours)</th>
                <th>% planned</th>
                <th v-for="week in weeks" :key="week">{{week}}</th>
            </thead>
            <tbody>
                <tr v-for="row in displayData" :key="row.user">
                    <td>{{ row.user }}</td>
                    <td>{{ row.updated_at }}</td>
                    <td>{{ row.area }}</td>
                    <td>{{ row.hoursDay }}</td>
                    <td>{{ row.availableHours }}</td>
                    <td>{{ row.usedHours }}</td>
                    <td>{{ row.scheduledHours }}</td>
                    <td>{{ row.pendingHours }}</td>
                    <td :class="{ 'alert': row.percentage < 50}">{{row.percentage}}</td>
                    <td v-for="(userWeek, index) in row.holidays" :key="row.user + '-' + index" :class="{ 'highlight': userWeek > 0}">{{userWeek}}</td>
                </tr>
            </tbody>
        </table>
        <p class="text-center">
            <a :href="downloadUrl" class="btn">Download Report</a>
        </p>
    </section>
</div>

<script src='js/holidaySummary.js'></script>

<?php
include("include/footer.php");
?>
