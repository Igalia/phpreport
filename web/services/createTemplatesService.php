<?php
/*
 * Copyright (C) 2016 Igalia, S.L. <info@igalia.com>
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
if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

require PHPREPORT_ROOT . "vendor/autoload.php";
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

use Phpreport\Web\services\TemplateService as TemplateService;

$request = trim(file_get_contents('php://input'));
$loginManager = new \LoginManager();
$service = new TemplateService($loginManager);

// send an XML mime header
header("Content-type: text/xml");

echo $service->createTemplate($request);
