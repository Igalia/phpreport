<?php
/*
 * Copyright (C) 2021 Igalia, S.L. <info@igalia.com>
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

use Phpreport\Web\services\HolidayService;

define('PHPREPORT_ROOT', __DIR__ . '/../../');

require_once(PHPREPORT_ROOT . "/vendor/autoload.php");
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="holiday-summary.csv"');

$loginManager = new \LoginManager();

$holidayService = new HolidayService($loginManager);

$usersAndWeeks = $holidayService->retrieveHolidaysSummary();

$fp = fopen('php://output', 'wb');

$today = date_create();
fputcsv($fp, array('Report generated at', $today->format('Y-m-d H:i:s e')), ';');
fputcsv($fp, array(''), ';');

$weeksLine = array_merge(array('User', 'Hours/day', 'Holidays(days)', 'Holidays(hours)', 'Pending (hours)', 'Planned (hours)', '% planned'), array_keys($usersAndWeeks["weeks"]));

fputcsv($fp, $weeksLine, ';');
foreach ($usersAndWeeks["holidays"] as $line) {
    if (count($line["holidays"]) == 0) {
        $line["holidays"] = array_fill(0, count($usersAndWeeks["weeks"]), 0);
    }
    fputcsv(
        $fp,
        array_merge(
            array(
                $line["user"],
                $line["hoursDay"],
                $line["availableDays"],
                $line["availableHours"],
                $line["pendingHours"],
                $line["usedHours"],
                $line["percentage"]
            ),
            $line["holidays"]
        ),
        ';'
    );
}
fclose($fp);
