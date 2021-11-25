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

$userLogin = $_GET['userLogin'] ?? NULL;
$loginManager = new \LoginManager();
$holidayService = new HolidayService($loginManager);

$datesRanges = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json; charset=utf-8');

try {
    $result = $holidayService->syncCalDAVCalendar($datesRanges);
    if (array_key_exists('error', $result)) {
        // user is logged out or doesn't have permission
        http_response_code(403);
    }
} catch (Exception $e) {
    http_response_code(500);
    $result = ['error' => "An error happened while syncing the calendar"];
}

echo json_encode($result);
