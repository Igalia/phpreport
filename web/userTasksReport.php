<?php
/*
 * Copyright (C) 2010-2015 Igalia, S.L. <info@igalia.com>
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

$sid = $_GET["sid"];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

$MENU_COORDINATION = ConfigurationParametersManager::getParameter('MENU_COORDINATION');


/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - User tasks report");
include_once("include/header.php");
include_once("include/sidebar.php");
$user = $_SESSION['user'];

//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script type='text/javascript'>\n";
echo "var userId = '" . $_SESSION['user']->getId() . "';\n";
echo "var user = '" . $user->getLogin() . "';\n";
if(LoginManager::hasExtraPermissions()) {
    echo "var admin = true; \n";
} else {
    echo "var admin = false; \n";
}
echo "var menuCoordination = '$MENU_COORDINATION';\n";
echo "</script>\n";
?>
<script src="js/include/sessionTracker.js"></script>
<script type="text/javascript" src="js/include/ExportableGridPanel.js"></script>
<script type="text/javascript" src="js/userTasksReport.js"></script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
