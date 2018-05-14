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
    define('PAGE_TITLE', "PhpReport - Working Hours Results Report");
    include_once("include/header.php");

include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');
$EXTRA_HOURS_WARNING_TRIGGER = ConfigurationParametersManager::getParameter('EXTRA_HOURS_WARNING_TRIGGER');
?>
<script>
var loggedInUser = '<?php echo $_SESSION['user']->getLogin(); ?>';
var extraHoursTrigger = '<?php echo $EXTRA_HOURS_WARNING_TRIGGER; ?>';
</script>
<script type="text/javascript" src="js/include/DateIntervalForm.min.js"></script>
<script type="text/javascript" src="js/include/ExportableGridPanel.min.js"></script>
<script src="js/viewWorkingHoursResultsReport.min.js"></script>

<div id="content">
</div>
<?php
/* Include the footer to close the header */
include("include/footer.php");
?>
