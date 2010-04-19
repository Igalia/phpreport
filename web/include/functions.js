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

// Various purposes functions

// Function for encoding special chars in xml
function xmlencode(string) {
    return string.replace(/\&/g,'&'+'amp;').replace(/</g,'&'+'lt;')
        .replace(/>/g,'&'+'gt;').replace(/\'/g,'&'+'apos;').replace(/\"/g,'&'+'quot;');
}

// Function for removing whitespaces from both the start and end of a string
function Trim(str)
{

    return LTrim(RTrim(str));

}

// Function for removing whitespaces from the start of a string
function LTrim(str)
{

    var i=0;
    while(str.charAt(i) == " ")
	i++;
    return str.substring(i);

}

// Function for removing whitespaces from the end of a string
function RTrim(str)
{

    var i=str.length-1;
    while(str.charAt(i) == " ")
	i--;
    return str.substring(0,i+1);

}
