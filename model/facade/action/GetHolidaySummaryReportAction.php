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
include_once(PHPREPORT_ROOT . '/model/facade/AdminFacade.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');

use Phpreport\Web\services\HolidayService;

class GetHolidaySummaryReportAction extends GetHolidayHoursBaseAction
{
    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL, Datetime $referenceDate = NULL, array $weeks = [])
    {
        parent::__construct($init, $end, $user);
        $this->preActionParameter = "GET_HOLIDAY_SUMMARY_REPORT_PREACTION";
        $this->postActionParameter = "GET_HOLIDAY_SUMMARY_REPORT_POSTACTION";
        $this->referenceDate = $referenceDate ?? new DateTime();
        $this->weeks = $weeks;
    }

    protected function doExecute()
    {
        $summary = $this->getHoursSummary($this->referenceDate);
        $today = new DateTime(date('Y-m-d'));
        $journeyHistories = \UsersFacade::GetUserJourneyHistories($this->user->getLogin());
        $startDate = array_map(fn ($history) => $history->getInitDate(), $journeyHistories);
        $startDate = $startDate && min($startDate)->format('Y') == date("Y") ? date_format(min($startDate), 'Y-m-d') : '';
        $leaves = HolidayService::mapHalfLeaves(\UsersFacade::GetScheduledHolidays(
            $this->init,
            $this->end,
            $this->user
        ), $journeyHistories);
        $summary = \UsersFacade::GetHolidayHoursSummary(
            $this->init,
            $this->end,
            $this->user,
            $this->end
        );
        $usedHours = $summary['usedHours'][$this->user->getLogin()];
        if (date('Y') == $this->end->format('Y')) {
            // If today's year the same as the one being filtered, we
            // need to use today as the end date for the summary, to avoid
            // counting leaves in the future as Used
            $relativeSummary = \UsersFacade::GetHolidayHoursSummary(
                $this->init,
                $this->end,
                $this->user,
                $today
            );
            $usedHours = $relativeSummary['usedHours'][$this->user->getLogin()];
        }
        $validJourney = array_filter(
            $journeyHistories,
            fn ($history) => $history->dateBelongsToHistory(date_create())
        );
        $validJourney = array_pop($validJourney);
        $validJourney = $validJourney ? $validJourney->getJourney() : 0;
        $leaves = HolidayService::groupByWeeks($leaves, $this->weeks, $journeyHistories);
        if (count($leaves) == 0) {
            $leaves = $this->weeks;
        }
        $areas = \UsersFacade::GetUserAreaHistories($this->user->getLogin());
        $currentArea = array_filter(
            $areas,
            fn ($area) => $area->dateBelongsToHistory(date_create())
        );
        $currentArea = array_pop($currentArea);
        if ($currentArea) {
            $currentArea = AdminFacade::GetAreaById($currentArea->getAreaId());
            $currentArea = $currentArea->getName();
        }
        return [
            'user' => $this->user->getLogin(),
            'updated_at' => $summary['updated_at'],
            'area' => $currentArea ?? '',
            'availableHours' => round($summary['availableHours'][$this->user->getLogin()], 2),
            'usedHours' => round($usedHours, 2),
            'pendingHours' => round($summary['pendingHours'][$this->user->getLogin()], 2),
            'plannedHours' => round($summary['usedHours'][$this->user->getLogin()], 2),
            'percentage' =>  $summary['availableHours'][$this->user->getLogin()] ? round(($summary['usedHours'][$this->user->getLogin()] / $summary['availableHours'][$this->user->getLogin()]) * 100, 2) : 0,
            'hoursDay' => $validJourney,
            'holidays' => $leaves,
        ];
    }
}
