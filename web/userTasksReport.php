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

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - User tasks report");
include_once("include/header.php");
$user = $_SESSION['user'];

//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script>\n";
echo "var userId = '" . $user->getId() . "';\n";
echo "var user = '" . $user->getLogin() . "';\n";
if(LoginManager::hasExtraPermissions()) {
    echo "var admin = true; \n";
} else {
    echo "var admin = false; \n";
}
echo "</script>\n";
?>
<script src="js/include/ExportableGridPanel.js"></script>
<script src="js/userTasksReport.js"></script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
