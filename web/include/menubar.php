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
$SHOW_MENU = LoginManager::isLogged();
$ISSUE_TRACKER_LINKS_TEXT = explode(',', ConfigurationParametersManager::getParameter('ISSUE_TRACKER_LINKS_TEXT'));
$ISSUE_TRACKER_LINKS_URL = explode(',', ConfigurationParametersManager::getParameter('ISSUE_TRACKER_LINKS_URL'));
?>

<link rel="stylesheet" type="text/css" href="include/menubar.css"/>
<script src="js/include/menubar.js"></script>

<ul id="menubar">
    <li id="icon">
        <img id="header-icon" alt="Header icon" src="include/images/header-icon-32.png"
            srcset="include/images/header-icon-64.png 2x, include/images/header-icon-32.png 1x"/>
    </li>

    <?php if ($SHOW_MENU) {?>
    <li><a href="tasks.php">Tasks</a></li>
    <li><a href="holidayManagement.php">Vacation Management</a></li>
    <li class="dropdown">
        <a href="#" class="dropdown-button">
            Reports
            <img class="menu-arrow" alt="Dropdown menu" src="include/images/menu-arrow.svg"/>
        </a>
        <ul class="dropdown-content">
            <li class='sprite-pencil'>
                <a href="userTasksReport.php">User tasks</a>
            </li>
            <li class="divider"></li>
            <li class="sprite-user-green">
                <a href="viewUserDetails.php">User details</a>
            </li>
            <li class="sprite-user">
                <a href="usersEvaluation.php">Users evaluation</a>
            </li>
            <li class="sprite-report-user">
                <a href="viewWorkingHoursResultsReport.php">Accumulated hours</a>
            </li>
            <li class="divider"></li>
            <li class="sprite-book-go">
                <a href="projectDetails.php">Project details</a>
            </li>
            <li class="sprite-book-open">
                <a href="projectsEvaluation.php">Project evaluation</a>
            </li>
            <li class="sprite-book">
                <a href="projectsSummary.php">Projects summary</a>
            </li>
            <li class="sprite-calendar-edit">
                <a href="holidaySummary.php">Vacation summary</a>
            </li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-button">
            Data management
            <img class="menu-arrow" alt="Dropdown menu" src="include/images/menu-arrow.svg"/>
        </a>
        <ul class="dropdown-content">
            <li class="sprite-user-edit">
                <a href="viewUsers.php">Users</a>
            </li>
            <li class="sprite-book-edit">
                <a href="projectManagement.php">Projects</a>
            </li>
            <li class="sprite-vcard-edit">
                <a href="customerManagement.php">Clients</a>
            </li>
            <li class="sprite-brick-edit">
                <a href="areaManagement.php">Areas</a>
            </li>
            <li class="sprite-building-edit">
                <a href="cityManagement.php">Cities</a>
            </li>
            <li class="sprite-calendar-edit">
                <a href="calendarManagement.php">Calendars</a>
            </li>
            <li class="sprite-script-edit">
                <a href="hourCompensationManagement.php">Hour compensations</a>
            </li>
            <li class="sprite-user-edit">
                <a href="longLeaves.php">Long Leaves</a>
            </li>
            <li class="sprite-brick-edit">
                <a href="settings.php">Application settings</a>
            </li>
        </ul>
    </li>
    <!-- Last items must be listed from right to left -->
    <li class="right"><a href="logout.php">Logout</a></li>
    <?php } // endif ($SHOW_MENU) ?>
    <li class="right"><a href="../help/user" target="blank">Help</a></li>
    <?php foreach ($ISSUE_TRACKER_LINKS_TEXT as $key => $text) { ?>
    <li class="right">
        <a href="<?php echo $ISSUE_TRACKER_LINKS_URL[$key] ?>" target="blank">
            <?php echo $text ?>
        </a>
    </li>
    <?php } // end foreach ($ISSUE_TRACKER_LINKS_TEXT) ?>
</ul>
