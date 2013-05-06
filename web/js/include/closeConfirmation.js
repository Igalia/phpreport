/*
 * Copyright (C) 2013 Igalia, S.L. <info@igalia.com>
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

/**
 * Add an event to ask for user confirmation before leaving the page in case
 * there are unsaved changes. It relies on the existence of a function called
 * isUnsaved() to know if there are unsaved changes.
 */
window.onbeforeunload = function () {
    //first check if:
    // 1. isUnsaved function exists
    // 2. call to isUnsaved returns true
    if(typeof(isUnsaved) == typeof(Function) && isUnsaved()) {
        return 'You will lose unsaved changes if you leave this page.';
    }
};
