<?php
/*
 * Copyright (C) 2009-2015 Igalia, S.L. <info@igalia.com>
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

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');
include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

$MENU_COORDINATION = ConfigurationParametersManager::getParameter('MENU_COORDINATION');
$VACATIONS_PROJECT = ConfigurationParametersManager::getParameter('VACATIONS_PROJECT');

$projects = ProjectsFacade::GetAllProjects();
foreach((array) $projects as $project) {
    if($project->getDescription() == $VACATIONS_PROJECT) {
        $VACATIONS_PROJECT_ID = $project->getId();
    }
}

$user = $_SESSION['user'];

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Tasks");
include("include/header.php");

/* If no date is specified, get client date via JS and reload */
if(!isset($_GET["date"])) {
    $date = new DateTime();
    $dateString = $date->format("Y-m-d");
    $serverDayOfMonth = $date->format("d");

    echo "<!-- Check if server and browser are in a different day due to timezones -->\n";
    echo "<script>\n";
    echo "var serverDayOfMonth = " . $serverDayOfMonth . ";\n";
    echo "var browserDate = new Date();\n";
    echo "if (browserDate.getDate() != serverDayOfMonth) {\n";
    echo "   window.location = \"tasks.php?date=\" + browserDate.format('Y-m-d');\n";
    echo "}\n";
    echo "</script>\n";
}
else {
    $dateString = $_GET["date"];
    $date = new DateTime($dateString);
}

/* Get the needed variables to be passed to the Javascript code */

//last task date
$lastTaskDate = TasksFacade::getLastTaskDate($user, $date);
if($lastTaskDate == NULL) {
    //defaults to the day before $date
    $lastTaskDate = clone $date;
    $lastTaskDate->sub(new DateInterval('P1D'));
}
$lastTaskDate = $lastTaskDate->format('Y-m-d');

//is current date enabled to write?
$forbidden = false;
if(!TasksFacade::IsWriteAllowedForDate($date)) {
    $forbidden = true;
}

//get user journey for the date
$currentJourney = 0;
$journeys = UsersFacade::GetUserJourneyHistoriesByIntervals($date, $date, $user->getId());
if(count($journeys)==1) {
    $currentJourney = $journeys[0]->getJourney();
}

//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script>\n";
echo "var lastTaskDate = Date.parseDate('" . $lastTaskDate . "', 'Y-m-d');\n";
echo "var forbidden = " . ($forbidden? "true": "false") . ";\n";
echo "var dateString = '" . $dateString . "';\n";
echo "var currentDate = Date.parseDate(dateString, 'Y-m-d');\n";
echo "var user = '" . $user->getLogin() . "';\n";
echo "var currentJourney = '$currentJourney';\n";
echo "var menuCoordination = '$MENU_COORDINATION';\n";
echo "var vacationsProjectId = '$VACATIONS_PROJECT_ID';\n";


echo "</script>\n";

?>
<script src="js/include/TasksStore.js"></script>
<script src="js/tasks.js"></script>
<script src="js/include/closeConfirmation.js"></script>

<div id="sidebar">
    <div id="summarypanel" class="auxiliarpanel">
    </div>
    <div id="calendarpanel" class="auxiliarpanel">
    </div>
    <div id="actionspanel" class="auxiliarpanel">
    </div>
</div>

<div id="content" class="tasks-content">
    <div id="tasks"></div>
    <div id="moreactions"></div>
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
