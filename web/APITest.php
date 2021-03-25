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

define('PHPREPORT_ROOT', __DIR__ . '/../');

/* We check authentication and authorization */
require_once(PHPREPORT_ROOT . '/web/auth.php');

/* Include the generic header and sidebar*/
define('PAGE_TITLE', "PhpReport - API test");
include("include/header.php");
?>

<style>
#container {
    margin-left: 5px;
}
input, textarea {
    display: block;
    width: 98%;
    border: 1px inset threedlightshadow;
    padding: 5px;
}
</style>
<div id="container">
    <input type="text" id="urlInput"></input>
    <textarea id="requestInput" rows="20"></textarea>
    <button id="sendButton">Send</button>
    <textarea id="responseInput" rows="20"></textarea>
</div>

<script src="js/APITest.js"></script>
