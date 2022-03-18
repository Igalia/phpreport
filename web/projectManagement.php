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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - Projects Management");
include_once("include/header.php");
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
include_once(PHPREPORT_ROOT . '/util/UnknownParameterException.php');
include_once(PHPREPORT_ROOT . '/util/LoginManager.php');
include_once(PHPREPORT_ROOT . '/model/vo/ProjectVO.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/CustomersFacade.php');
include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');

// We retrieve the Areas
$areas = AdminFacade::GetAllAreas();
$customers = CustomersFacade::GetAllCustomers();

//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script>\n";
echo "var areasArray = [";
foreach((array)$areas as $area) {
    $areaName = json_encode($area->getName());
    echo "[{$area->getId()}, {$areaName}],";
}
echo "];\n";
echo "var customersArray = [";
foreach((array)$customers as $customer) {
    $customerName = addslashes($customer->getName());
    echo "[{$customer->getId()}, '{$customerName}'],";
}
echo "];\n";
echo "</script>\n";
?>
<script src="js/projectManagement.js"></script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
