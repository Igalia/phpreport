<?php
/*
 * Copyright (C) 2012 Igalia, S.L. <info@igalia.com>
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

/** Server-side code for hour compensation management screen.
 *
 * @filesource
 * @package PhpReport
 * @subpackage web
 * @author Jacobo Aragunde Pérez <jaragunde@igalia.com>
 */


define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
$sid = $_GET["sid"];
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar */
define('PAGE_TITLE', "PhpReport - Hour Compensation Management");
include_once("include/header.php");
include_once("include/sidebar.php");

?>

<script type="text/javascript" src="js/hourCompensationManagement.js"></script>

<div id="content">
</div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
