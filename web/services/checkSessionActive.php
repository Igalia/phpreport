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

/** login web service.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 */
define('PHPREPORT_ROOT', __DIR__ . '/../../');
/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

if (LoginManager::isLogged()) {
    http_response_code(200);
} else {
    http_response_code(401);
}