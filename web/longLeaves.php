<?php
/*
 * Copyright (C) 2022 Igalia, S.L. <info@igalia.com>
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

require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Vacation Management");
include_once("include/header.php");

?>

<link rel="stylesheet" type="text/css" href="include/form.css" />

<script src="vuejs/vue.min.js"></script>

<div id="longLeavesForm">
    <section class="container">
        <h1>Create new period of Long Leaves</h1>
        <form>
            <div class="field">
                <label class="fieldLabel">Type of leave</label>
                <select v-model="selectedProject">
                    <option v-for="project in projects" :value="project.value">
                        {{ project.text }}
                    </option>
                </select>
            </div>
            <div class="field">
                <label class="fieldLabel">User</label>
                <select v-model="selectedUser">
                    <option v-for="user in users" :value="user.value">
                        {{ user.text }}
                    </option>
                </select>
            </div>
            <div class="field">
                <label class="fieldLabel">Start Date</label>
                <input id="initDate" type="date" v-model="initDate" />
            </div>
            <div class="field">
                <label class="fieldLabel">End Date</label>
                <input id="endDate" type="date" v-model="endDate" />
            </div>
            <input class="submitBtn" type="button" @click="submitForm" value="Create Leave" />
        </form>
    </section>
    <div v-for="message in serverMessages" :class="message.classes" :key="message">{{ message.text }}</div>
</div>

<script src='js/longLeaves.js'></script>

<?php
include("include/footer.php");
?>
