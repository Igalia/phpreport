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

<link rel="stylesheet" type="text/css" href="include/calendar.css" />

<script src="vuejs/vue.min.js"></script>
<script src="vuejs/v-calendar.2.3.2.min.js"></script>

<div id="holidaysApp">
    <nav class="submenu">
        <li><a href="#my_holidays" id="personalCalendar" v-on:click="switchMode">My holidays</a></li>
        <li><a href="#team_calendar" id="teamCalendar" v-on:click="switchMode">Team calendar</a></li>
    </nav>
    <div class="holidayContainer">
        <div class="sidebar">
            <div class="holidaysList">
                <h2 class="sidebarTitle">Holidays Summary for {{ currentYear }}</h2>
                <div v-if="!isEditing" class="autocompleteContainer">
                    <input
                        class="autocompleteSearchInput"
                        type="text"
                        v-model="searchUser"
                        placeholder="Select a user"
                        @focus="showOptions"
                        @keyup="filterUser"
                        @focusout="hideOptions"
                        @keyup.13="onSelectUser(activeUser)"
                        @keyup.38="prevUser"
                        @keyup.40="nextUser"
                    />
                    <ul :class="{ 'hidden': !autocompleteIsActive, 'autocomplete': true}" id="usersDropdown">
                        <li v-for="(user, index) in usersList" class="autocompleteItem" v-on:click="onSelectUser(index)">
                            <button :class="{ 'active': index == activeUser, 'autocompleteItemBtn': true}">{{ user.name }}</button>
                        </li>
                    </ul>
                </div>
                <table class="summary">
                    <tr>
                        <td>Available for the year</td>
                        <td class="text-right">{{ summary.availableHolidays }}</td>
                    </tr>
                    <tr>
                        <td>Enjoyed</td>
                        <td class="text-right">{{ summary.enjoyedHolidays }}</td>
                    </tr>
                    <tr>
                        <td>Scheduled</td>
                        <td class="text-right">{{ summary.scheduledHolidays }}</td>
                    </tr>
                    <tr>
                        <td>Pending</td>
                        <td class="text-right">{{ summary.pendingHolidays }}</td>
                    </tr>
                </table>
                <div v-if="isEditing">
                    <p class="warning info">
                        <strong>TIP:</strong> Double click on single dates if you want to delete existing holidays.
                    </p>
                    <p class="text-center">
                        <button class="btn" ref="syncBtn" v-on:click="syncCalendar">Sync with Sogo</button>
                        <button class="btn" ref="saveBtn" v-on:click="onSaveClick">Save Holidays</button>
                    </p>
                </div>
            </div>
            <div class="holidaysList">
                <h2 class="sidebarTitle">Days booked per week</h2>
                <table class="summary">
                    <tr v-for="week in weeksList" :key="week.weekNumber">
                        <td>{{ week.weekNumber }}</td>
                        <td class="text-right">{{ week.total }} {{ week.total > 1 ? 'days' : 'day' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="calendar">
            <div v-if="isEditing">
                <v-date-picker ref="calendar" is-expanded :from-page="fromPage" is-range v-model="range" :attributes="ranges" :first-day-of-week="2" :rows="3" :columns="$screens({ default: 2, lg: 4 })" show-iso-weeknumbers :select-attribute="selectAttribute" @dayclick="onDayClick" @update:from-page="updateCurrentYear" :min-date="init" :max-date="end" />
            </div>
            <div v-show="!isEditing">
                <v-calendar is-range is-expanded :from-page="fromPage" v-model="teamRange" :attributes="teamAttributes" :first-day-of-week="2" :rows="3" :columns="$screens({ default: 2, lg: 4 })" show-iso-weeknumbers @update:from-page="updateCurrentYear" :min-date="init" :max-date="end" />
            </div>
        </div>
        <div class="snackbarWrapper">
            <div id="snackBar" v-for="message in serverMessages" :class="message.classes" :key="message">{{ message.text }}</div>
        </div>
    </div>
</div>

<script src='js/holidayManagement.js'></script>

<?php
include("include/footer.php");
?>
