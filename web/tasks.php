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

$user = $_SESSION['user'];

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Tasks");
include("include/header.php");
include("include/sidebar.php");

/* Get the needed variables to be passed to the Javascript code */

//selected date
if(isset($_GET["date"]))
    $dateString = $_GET["date"];
else
    $dateString = date("Y-m-d");
$date = new DateTime($dateString);

//last task date
$lastTaskDate = TasksFacade::getLastTaskDate($user, $date);
if($lastTaskDate == NULL) {
    //defaults to the day before $date
    $lastTaskDate = $date->sub(new DateInterval('P1D'));
}
$lastTaskDate = $lastTaskDate->format('Y-m-d');

//is current date enabled to write?
$forbidden = false;
if(!TasksFacade::IsWriteAllowedForDate($date)) {
    $forbidden = true;
}

//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script type='text/javascript'>\n";
echo "var lastTaskDate = Date.parseDate('" . $lastTaskDate . "', 'Y-m-d');\n";
echo "var forbidden = " . ($forbidden? "true": "false") . ";\n";
echo "var date = '" . $dateString . "';\n";
echo "var user = '" . $user->getLogin() . "';\n";
echo "</script>\n";

?>
<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus.js"></script>
<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus-holidays.js"></script>
<script src="js/include/TasksStore.js"></script>
<script src="js/tasks.js"></script>
<script src="js/include/closeConfirmation.js"></script>

<div id="summarypanel" class="auxiliarpanel">
</div>
<div id="calendarpanel" class="auxiliarpanel">
</div>
<div id="actionspanel" class="auxiliarpanel">
</div>

<div id="content" style="margin-left: 215px;">
    <div id="tasks"></div>
    <div id="moreactions"></div>
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
