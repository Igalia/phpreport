<?php
/*
 * Copyright (C) 2009-2018 Igalia, S.L. <info@igalia.com>
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

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
$MENU_COORDINATION = ConfigurationParametersManager::getParameter('MENU_COORDINATION');
?>

<link rel="stylesheet" type="text/css" href="include/menubar.css"/>
<script type="text/javascript" src="js/include/menubar.js"></script>

<ul id="menubar">
    <li><a href="tasks.php">Tasks</a></li>
    <?php if ($MENU_COORDINATION) {?>
    <li class="dropdown">
        <a href="#" class="dropdown-button" >Coordination</a>
        <ul class="dropdown-content">
            <a href="xptracker-summary.php">XP Tracker</a>
            <a href="analysistracker-summary.php">Analysis Tracker</a>
        </ul>
    </li>
    <?php } // endif ($MENU_COORDINATION) ?>
    <li class="dropdown">
        <a href="#" class="dropdown-button" >Reports</a>
        <ul class="dropdown-content">
            <a href="userTasksReport.php">User tasks</a>
            <a href="viewUserDetails.php">User details</a>
            <a href="usersEvaluation.php">Users evaluation</a>
            <a href="viewWorkingHoursResultsReport.php">Accumulated hours</a>
            <a href="projectDetails.php">Project details</a>
            <a href="projectsEvaluation.php">Project evaluation</a>
            <a href="projectsSummary.php">Projects summary</a>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-button" >Data management</a>
        <ul class="dropdown-content">
            <a href="viewUsers.php">Users</a>
            <a href="viewProjects.php">Projects</a>
            <a href="viewCustomers.php">Clients</a>
            <a href="viewAreas.php">Areas</a>
            <a href="cityManagement.php">Cities</a>
            <a href="calendarManagement.php">Calendars</a>
            <a href="hourCompensationManagement.php">Hour compensations</a>
            <a href="settings.php">Application settings</a>
        </ul>
    </li>
    <li><a href="../help/user" target="blank">Help</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
