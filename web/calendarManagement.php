<?php
/*
 * Copyright (C) 2011 Igalia, S.L. <info@igalia.com>
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
$sid = $_GET["sid"];
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Calendar Management");
include_once("include/header.php");
include_once("include/sidebar.php");

/* Retrieve cities */
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
$cities = AdminFacade::GetAllCities();

echo '<script type="text/javascript">';
echo 'var citiesArray = [';
foreach((array)$cities as $city)
    echo "[{$city->getId()}, '" . ucwords($city->getName()) . "'],";
echo '];';
echo '</script>';

?>

<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus.js"></script>
<script src="include/ext.ux.datepickerplus/ext.ux.datepickerplus-holidays.js"></script>
<script type="text/javascript" src='js/calendarManagement.js'></script>

<div id="sidebar-panel" class="auxiliarpanel"></div>
<div id="content" style="margin-left: 215px;"></div>

<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
