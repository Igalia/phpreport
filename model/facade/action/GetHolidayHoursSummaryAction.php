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

include_once(PHPREPORT_ROOT . '/model/facade/action/GetHolidayHoursBaseAction.php');

class GetHolidayHoursSummaryAction extends GetHolidayHoursBaseAction
{
    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL, Datetime $today = NULL)
    {
        parent::__construct($init, $end, $user);
        $this->preActionParameter = "GET_HOLIDAY_HOURS_SUMMARY_PREACTION";
        $this->postActionParameter = "GET_HOLIDAY_HOURS_SUMMARY_POSTACTION";
        $this->today = $today ?? new DateTime();
    }

    protected function doExecute()
    {
        return $this->getHoursSummary();
    }
}
