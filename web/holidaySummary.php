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
define('PAGE_TITLE', "PhpReport - Holiday Management");
include_once("include/header.php");

?>

<link rel="stylesheet" type="text/css" href="include/report.css" />

<script src="vuejs/vue.min.js"></script>
<script src="vuejs/v-calendar.2.3.2.min.js"></script>

<div id="holidaySummaryReport">
    <table class="report">
        <thead slot="head">
            <th>User</th>
            <th>Hours/day</th>
            <th>Holidays (days)</th>
            <th>Holidays (hours)</th>
            <th>Pending (hours)</th>
            <th>Planned (hours)</th>
            <th>% planned</th>
            <th v-for="week in weeks" :key="week">{{week}}</th>
        </thead>
        <tbody>
            <tr v-for="row in displayData" :key="row.id">
                <td>{{ row.user }}</td>
                <td>{{row.hoursDay}}</td>
                <td>{{row.availableDays}}</td>
                <td>{{row.availableHours}}</td>
                <td>{{row.pendingHours}}</td>
                <td>{{row.usedHours}}</td>
                <td :class="{ 'alert': row.percentage < 50}">{{row.percentage}}</td>
                <td v-for="userWeek in row.holidays" :key="userWeek" :class="{ 'highlight': userWeek > 0}">{{userWeek}}</td>
            </tr>
        </tbody>
    </table>
    <p class="text-center">
        <a href="services/getHolidaySummary.php?format=csv" class="btn">Download Report</a>
    </p>
</div>

<script src='js/holidaySummary.js'></script>

<?php
include("include/footer.php");
?>
