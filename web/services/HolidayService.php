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

namespace Phpreport\Web\services;

use Phpreport\Model\facade;

if (!defined('PHPREPORT_ROOT')) define('PHPREPORT_ROOT', __DIR__ . '/../../');

include_once(PHPREPORT_ROOT . '/web/services/WebServicesFunctions.php');
include_once(PHPREPORT_ROOT . '/model/facade/ProjectsFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/TasksFacade.php');
include_once(PHPREPORT_ROOT . '/model/facade/UsersFacade.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
require_once(PHPREPORT_ROOT . '/util/LoginManager.php');

class HolidayService
{
    private \LoginManager $loginManager;

    public function __construct(
        \LoginManager $loginManager
    ) {
        $this->loginManager = $loginManager;
    }

    function isWeekend(string $date): bool
    {
        return (date('N', strtotime($date)) >= 6);
    }

    /** Group dates into date ranges
     *
     * It receives an array of dates in the ISO format YYYY-MM-DD
     * and group them into date ranges if they are close by 1 day.
     * 
     * Single dates are converted into a range with the same start
     * and end.
     * 
     * Examples:
     * 
     * 1. the array ['2021-01-01', '2021-01-02'] should return a
     * single range of dates that starts in 2021-01-01 and ends in
     * 2021-01-02: 
     * [
     *    [
     *        'start' => '2021-01-01',
     *        'end' => '2021-01-02'
     *    ]
     * ]
     * 
     * 2. ['2021-01-01'] will be converted in a range with the same
     * start and end date:
     * [
     *    [
     *        'start' => '2021-01-01',
     *        'end' => '2021-01-01'
     *    ]
     * ]
     */
    public function datesToRanges(array $vacations): array
    {
        if (count($vacations) == 0) {
            return [];
        }

        $start = $vacations[0];
        $last_date = $start;
        $ranges = array();
        for ($i = 0; $i < count($vacations); $i++) {
            $previousDate = date_create($last_date);
            $currentDate = date_create($vacations[$i]);
            $interval = date_diff($previousDate, $currentDate);

            if ($interval->days > 1) {
                $ranges[] = ['start' => $start, 'end' => $last_date];
                $start = $vacations[$i];
                // If it's the last date of the array, it is not part of the range
                // so it should also be added as a new range
                if ($i + 1 == count($vacations)) {
                    $ranges[] = ['start' => $vacations[$i], 'end' => $vacations[$i]];
                }
            } elseif ($i + 1 == count($vacations)) {
                // If it's the last element and the interval is 1 or 0, it means it's a single
                // element array, so we should create a range for it 
                $ranges[] = ['start' => $start, 'end' => $vacations[$i]];
            }
            $last_date = $vacations[$i];
        }
        return $ranges;
    }

    /**
     * Function used to pretty print time. From hours to Days d hours:minutes
     */
    static function formatHours(float $time, float $journey, int $limit): string
    {
        $negative = ($time < 0);
        $work_days = false;
        $time = abs($time);
        $time = round($time, 2);
        $time = $time * 60;

        if ($journey > 0 && $time > $limit * $journey * 60) {
            $work_days = intval($time / ($journey * 60));
            $hours = intval(($time - ($work_days * $journey * 60)) / 60);
            $minutes = intval($time - $hours * 60 - $work_days * $journey * 60);
        } else {
            $hours = intval($time / 60);
            $minutes = intval($time - ($hours * 60));
        }

        if ($minutes >= 60) {
            $minutes = $minutes - 60;
            $hours = $hours + 1;
        }

        if ($hours < 10) {
            $hours = "0" . $hours;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }

        if ($work_days)
            $formatedHours = $work_days . " d " . $hours . ":" . $minutes;
        else
            $formatedHours = $hours . ":" . $minutes;

        if ($negative)
            $formatedHours = "-" . $formatedHours;

        return $formatedHours;
    }

    static function getWeeksFromYear($year = NULL): array
    {
        $weeks = [];
        $year = $year ?? date('Y');
        $first_week = (int)date('W', mktime(0, 0, 0, 1, 1, $year));
        $last_week = (int) date('W', mktime(0, 0, 0, 12, 31, $year));
        if ($first_week == 52 || $first_week == 53) {
            $weeks[($year - 1) . "W" . $first_week] = 0;
            $first_week = 1;
        }
        if ($last_week == 1) {
            $weeks[($year + 1) . "W01"] = 0;
            // get the week number of the second to last week of the year
            $last_week = date('W', mktime(0, 0, 0, 12, 24, $year));
        }
        for ($i = $first_week; $i <= (int) $last_week; $i++) {
            $weekNumber = ($i < 10) ? "0" . $i : $i;
            $weeks[$year . "W" . $weekNumber] = 0;
        }
        // Make sure the weeks are sorted
        ksort($weeks);
        return $weeks;
    }

    static function groupByWeeks(array $leavesDetails, $weeks = []): array
    {
        if (count($leavesDetails) == 0) return [];
        $dates = array_keys($leavesDetails);
        $previous_week = date("o\WW", strtotime($dates[0]));
        $weeks[$previous_week] = $leavesDetails[$dates[0]]['amount'] ?? 1;
        for ($i = 1; $i < count($dates); $i++) {
            $current_week = date("o\WW", strtotime($dates[$i]));
            if ($current_week == $previous_week) {
                $weeks[$current_week] += $leavesDetails[$dates[$i]]['amount'] ?? 1;
            } else {
                $weeks[$current_week] = $leavesDetails[$dates[$i]]['amount'] ?? 1;
                $previous_week = $current_week;
            }
            $weeks[$current_week] = round($weeks[$current_week], 2);
        }
        return $weeks;
    }

    static function mapHalfLeaves($dates, array $journeyHistories): array
    {
        foreach ($dates as $day => $duration) {
            $validJourney = array_filter($journeyHistories, fn ($history) => $history->dateBelongsToHistory(
                date_create($day)
            ));
            if (count($validJourney) == 0) continue;
            $validJourney = array_pop($validJourney);
            if (($validJourney->getJourney() * 60) > ($duration['end'] - $duration['init'])) {
                $dates[$day]['isPartialLeave'] = True;
                $dates[$day]['amount'] = round((($duration['end'] - $duration['init']) / ($validJourney->getJourney() * 60)), 2);
            }
        }
        return $dates;
    }

    static function getDaysBetweenDates(string $init, string $end): array
    {
        $begin = new \DateTime($init);
        $end = new \DateTime($end);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);
        $dates = [];

        foreach ($daterange as $date) {
            $dates[] = $date->format("Y-m-d");
        }

        return $dates;
    }

    public function getUserVacationsRanges(string $init = NULL, string $end = NULL, $sid = NULL, $userLogin = NULL): array
    {
        if (!$this->loginManager::isLogged($sid)) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->loginManager::isAllowed($sid)) {
            return ['error' => 'Forbidden service for this User'];
        }

        $init = date_create($init ?? "1900-01-01");
        $end = date_create($end ?? date('Y') . "-12-31");

        $userVO = new \UserVO();
        $userVO->setLogin($userLogin ?? $_SESSION['user']->getLogin());

        $journeyHistories = \UsersFacade::GetUserJourneyHistories($userVO->getLogin());

        $vacations = \UsersFacade::GetScheduledHolidays($init, $end, $userVO);
        $vacations = $this::mapHalfLeaves($vacations, $journeyHistories);

        return [
            'dates' => $vacations,
            'ranges' => $this->datesToRanges(array_keys($vacations)),
            'weeks' => $this->groupByWeeks($vacations)
        ];
    }

    public function deleteVacations(array $daysToDelete, \UserVO $userVO, int $holidayProjectId): array
    {
        $failed = [];
        foreach ($daysToDelete as &$day) {
            $tasks = \TasksFacade::GetUserTasksByLoginDate($userVO, new \DateTime($day));
            $holidayTasks = array_filter($tasks, fn ($task) => $task->getProjectId() == $holidayProjectId);
            if (\TasksFacade::DeleteReports($holidayTasks) == -1)
                $failed[] = $day;
        }
        unset($day);
        return [
            'deleted' => array_diff($daysToDelete, $failed),
            'failed' => $failed
        ];
    }

    public function createVacations(array $daysToCreate, \UserVO $userVO, int $holidayProjectId): array
    {
        $journeyHistories = \UsersFacade::GetUserJourneyHistories($userVO->getLogin());
        $failed = [];
        foreach ($daysToCreate as &$day) {
            if ($this->isWeekend($day)) continue;

            $currentDay = date_create($day);
            $validJourney = array_filter($journeyHistories, fn ($history) =>  $history->dateBelongsToHistory(
                $currentDay
            ));
            if (count($validJourney) != 1) {
                $failed[] = $day;
                continue;
            }
            // There must be only one journey, so get the first element
            $validJourney = array_pop($validJourney);

            $taskVO = new \TaskVO();
            $taskVO->setDate($currentDay);
            $taskVO->setInit(0);
            $taskVO->setEnd($validJourney->getJourney() * 60);
            $taskVO->setProjectId($holidayProjectId);
            $taskVO->setUserId($userVO->getId());
            if (\TasksFacade::CreateReport($taskVO) == -1)
                $failed[] = $day;
        }
        unset($day);
        return [
            'created' => array_diff($daysToCreate, $failed),
            'failed' => $failed
        ];
    }

    public function updateUserVacations(array $vacations, string $init, string $end): array
    {
        if (!$this->loginManager::isLogged()) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->loginManager::isAllowed()) {
            return ['error' => 'Forbidden service for this User'];
        }

        if (!$init || !$end) {
            return ['error' => 'Init and end dates are mandatory'];
        }

        $userVO = new \UserVO();
        $userVO->setLogin($_SESSION['user']->getLogin());
        $userVO->setId($_SESSION['user']->getId());

        $existingVacations = array_keys(\UsersFacade::GetScheduledHolidays(
            date_create($init),
            date_create($end),
            $userVO
        ));
        $holidayProjectId = \TasksFacade::GetVacationsProjectId();
        $daysToDelete = array_diff($existingVacations, $vacations);
        $resultDeleted = $this->deleteVacations($daysToDelete, $userVO, $holidayProjectId);

        $daysToCreate = array_diff($vacations, $existingVacations);
        $resultCreation = $this->createVacations($daysToCreate, $userVO, $holidayProjectId);

        return [
            "datesAndRanges" => $this->getUserVacationsRanges($init, $end),
            "resultCreation" => $resultCreation,
            "resultDeleted" => $resultDeleted
        ];
    }

    public function syncCalDAVCalendar(array $datesRanges = [])
    {
        if (!$this->loginManager::isLogged()) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->loginManager::isAllowed()) {
            return ['error' => 'Forbidden service for this User'];
        }

        $userVO = new \UserVO();
        $userVO->setLogin($_SESSION['user']->getLogin());
        $userVO->setId($_SESSION['user']->getId());

        facade\CalDAVCalendarFacade::SyncCalendar($userVO, $datesRanges);
        return ['message' => 'Calendar synced'];
    }

    public function retrieveHolidaysSummary(string $year = NULL): array
    {
        if (!$this->loginManager::isLogged()) {
            return ['error' => 'User not logged in'];
        }

        if (!$this->loginManager::isAllowed()) {
            return ['error' => 'Forbidden service for this User'];
        }

        $year = $year ?? date('Y');
        $init = date_create($year . "-01-01");
        $end = date_create($year . "-12-31");

        $users = \UsersFacade::GetAllActiveUsers($filterEmployees = true);
        $weeks = $this::getWeeksFromYear($year);
        $holidays = [];
        foreach ($users as &$user) {
            $holidays[$user->getLogin()] = \UsersFacade::GetHolidaySummaryReport(
                $init,
                $end,
                $user,
                $end,
                $weeks
            );
        }
        unset($user);
        asort($holidays);
        return [
            "holidays" => $holidays,
            "weeks" => $weeks
        ];
    }
}
