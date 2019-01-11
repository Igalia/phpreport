<?php
/*
 * Copyright (C) 2009-2019 Igalia, S.L. <info@igalia.com>
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

$pid = $_GET['pid'];

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Define title and include the generic header */
define('PAGE_TITLE', "PhpReport - Project details report");
include_once("include/header.php");

include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');

$project = ProjectsFacade::GetCustomProject($pid);

// We are not allowing staff users to view all project details
$projectAssignedUsers = ProjectsFacade::GetProjectUsers($pid);
if(!LoginManager::hasExtraPermissions()) {
    $userCanViewProject = false;
    foreach ( $projectAssignedUsers as $userVO ) {
        if ( $userVO->getLogin() == $_SESSION['user']->getLogin() ) {
            $userCanViewProject = true;
            break;
        }
    }
    if(!$userCanViewProject) {
        echo "You are not allowed to access this page";
        return;
    }
}
?>
<script src="js/include/DateIntervalForm.min.js"></script>
<script src="js/include/ExportableGridPanel.min.js"></script>
<?php
//output vars as JS code
echo "<!-- Global variables extracted from the PHP side -->\n";
echo "<script>\n";
echo "var projectData = {\n";
echo "    description: '" . $project->getDescription() . "',\n";
echo "    id: " . $project->getId() . ",\n";
echo "    customerName: '" . $project->getCustomerName() . "',\n";
echo "    estimatedHours: '" . $project->getEstHours() . "',\n";
echo "    active: " . ($project->getActivation()? "true":"false") . ",\n";
echo "    movedHours: '" . $project->getMovedHours() . "',\n";
echo "    invoice: '" . $project->getInvoice() . "',\n";
echo "    type: '" . $project->getType() . "',\n";

echo "    finalEstimatedHours: '" . $project->getFinalEstHours() . "',\n";
echo "    workedHours: '" . $project->getWorkedHours() . "',\n";
echo "    workDeviation: '" . round($project->getAbsDev(), 2, PHP_ROUND_HALF_DOWN) . "',\n";
echo "    workDeviationPercent: '" . round($project->getPercDev(), 2, PHP_ROUND_HALF_DOWN) . "',\n";

echo "    estInvoice: '" . round($project->getEstHourInvoice(), 2, PHP_ROUND_HALF_DOWN) . "',\n";
echo "    currentInvoice: '" . round($project->getWorkedHourInvoice(), 2, PHP_ROUND_HALF_DOWN) . "',\n";
echo "    invoiceDeviation: '" . round($project->getWorkedHourInvoiceAbsoluteDeviation(), 2, PHP_ROUND_HALF_DOWN) . "',\n";
echo "    invoiceDeviationPercent: '" . round($project->getWorkedHourInvoiceRelativeDeviation(), 2, PHP_ROUND_HALF_DOWN) . "',\n";

if (is_null($project->getInit())) {
    echo "    initDate: '',\n";
}
else {
    echo "    initDate: Date.parseDate('" .
        $project->getInit()->format('Y-m-d') . "', 'Y-m-d'),\n";
}

if (is_null($project->getEnd())) {
    echo "    endDate: '',\n";
}
else {
    echo "    endDate: Date.parseDate('" .
        $project->getEnd()->format('Y-m-d') . "', 'Y-m-d'),\n";
}
echo "};\n";
echo "</script>\n";
?>
<script src="js/projectDetailsReport.min.js"></script>

<div id="content">
</div>
<div id="variables"/>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
