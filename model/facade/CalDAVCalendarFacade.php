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

namespace Phpreport\Model\facade;

include_once(PHPREPORT_ROOT . '/model/facade/action/SyncCalendarAction.php');

abstract class CalDAVCalendarFacade
{
    static function SyncCalendar(\UserVO $userVO, array $datesRanges = [])
    {
        $action = new \SyncCalendarAction($userVO, $datesRanges);
        return $action->execute();
    }
}
