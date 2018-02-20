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
$MENU_COORDINATION = ConfigurationParametersManager::getParameter('MENU_COORDINATION');
?>

<link rel="stylesheet" type="text/css" href="include/menubar.css"/>
<script type="text/javascript" src="js/include/menubar.js"></script>

<ul id="menubar">
    <img id="header-icon"
        srcset="include/images/header-icon-64.png 2x, include/images/header-icon-32.png 1x"
        src="include/images/header-icon-32.png" />

    <?php if ($SHOW_MENU) {?>
    <li><a href="tasks.php">Tasks</a></li>
    <?php if ($MENU_COORDINATION) {?>
    <li class="dropdown">
        <a href="#" class="dropdown-button">
            Coordination
            <img src="ext/resources/images/default/button/arrow.gif" />
        </a>
        <ul class="dropdown-content">
            <a href="xptracker-summary.php">
                <img class="silk-sitemap" src="include/icons/s.gif" />
                XP Tracker
            </a>
            <a href="analysistracker-summary.php">
                <img class="silk-sitemap-color" src="include/icons/s.gif" />
                Analysis Tracker
            </a>
        </ul>
    </li>
    <?php } // endif ($MENU_COORDINATION) ?>
    <li class="dropdown">
        <a href="#" class="dropdown-button">
            Reports
            <img src="ext/resources/images/default/button/arrow.gif" />
        </a>
        <ul class="dropdown-content">
            <a href="userTasksReport.php">
                <img class="silk-pencil" src="include/icons/s.gif" />
                User tasks
            </a>
            <li class="divider"></li>
            <a href="viewUserDetails.php">
                <img class="silk-user-green" src="include/icons/s.gif" />
                User details
            </a>
            <a href="usersEvaluation.php">
                <img class="silk-user" src="include/icons/s.gif" />
                Users evaluation
            </a>
            <a href="viewWorkingHoursResultsReport.php">
                <img class="silk-report-user" src="include/icons/s.gif" />
                Accumulated hours
            </a>
            <li class="divider"></li>
            <a href="projectDetails.php">
                <img class="silk-book-go" src="include/icons/s.gif" />
                Project details
            </a>
            <a href="projectsEvaluation.php">
                <img class="silk-book-open" src="include/icons/s.gif" />
                Project evaluation
            </a>
            <a href="projectsSummary.php">
                <img class="silk-book" src="include/icons/s.gif" />
                Projects summary
            </a>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-button">
            Data management
            <img src="ext/resources/images/default/button/arrow.gif" />
        </a>
        <ul class="dropdown-content">
            <a href="viewUsers.php">
                <img class="silk-user-edit" src="include/icons/s.gif" />
                Users
            </a>
            <a href="viewProjects.php">
                <img class="silk-book-edit" src="include/icons/s.gif" />
                Projects
            </a>
            <a href="viewCustomers.php">
                <img class="silk-vcard-edit" src="include/icons/s.gif" />
                Clients
            </a>
            <a href="viewAreas.php">
                <img class="silk-brick-edit" src="include/icons/s.gif" />
                Areas
            </a>
            <a href="cityManagement.php">
                <img class="silk-building-edit" src="include/icons/s.gif" />
                Cities
            </a>
            <a href="calendarManagement.php">
                <img class="silk-calendar-edit" src="include/icons/s.gif" />
                Calendars
            </a>
            <a href="hourCompensationManagement.php">
                <img class="silk-script-edit" src="include/icons/s.gif" />
                Hour compensations
            </a>
            <a href="settings.php">
                <img class="silk-brick-edit" src="include/icons/s.gif" />
                Application settings
            </a>
        </ul>
    </li>
    <!-- Last items must be listed from right to left -->
    <li class="right"><a href="logout.php">Logout</a></li>
    <?php } // endif ($SHOW_MENU) ?>
    <li class="right"><a href="../help/user" target="blank">Help</a></li>
</ul>
