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

/** Get the session user. It takes no parameters, and returns XML like this:
 *    <?xml version="1.0"?>
 *    <user><id>1</id><login>user</login></user>
 * It will return HTTP 401 in case the session is not open.
 *
 * @filesource
 * @package PhpReport
 * @subpackage services
 */
define('PHPREPORT_ROOT', __DIR__ . '/../../');
/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

$user = LoginManager::isLogged();
if ($user) {
    $string = "<user><id>" . $user->getId() . "</id>";
    $string .= "<login>" . $user->getLogin() . "</login></user>";

    $xml = simplexml_load_string($string);
    header("Content-type: text/xml");
    echo $xml->asXML();
} else {
    http_response_code(401);
}
