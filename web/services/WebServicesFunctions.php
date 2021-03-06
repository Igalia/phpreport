<?php
/*
 * Copyright (C) 2009 Igalia, S.L. <info@igalia.com>
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


function escape_string($string)
{
    return htmlentities($string, ENT_XML1);
}


function unescape_string($string)
{
    return html_entity_decode($string, ENT_XML1);
}


function authenticate($login, $sid = NULL)
{
    if ($sid)
        session_id($sid);

    session_start();

    if (empty($_SESSION['user']))
        return NULL;
    elseif (($_SESSION['user']->getLogin()) != $login)
        return NULL;

    return $_SESSION['user'];

}
