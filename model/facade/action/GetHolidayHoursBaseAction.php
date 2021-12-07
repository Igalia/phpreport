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

include_once(PHPREPORT_ROOT . '/model/facade/action/Action.php');
include_once(PHPREPORT_ROOT . '/model/dao/DAOFactory.php');
include_once(PHPREPORT_ROOT . '/model/vo/UserVO.php');
include_once(PHPREPORT_ROOT . '/util/ConfigurationParametersManager.php');

abstract class GetHolidayHoursBaseAction extends Action
{
    private ?UserVO $user;
    private DateTime $init;
    private DateTime $end;

    public function __construct(DateTime $init, DateTime $end, ?UserVO $user = NULL)
    {
        $this->init = $init;
        $this->end = $end;
        $this->user = $user;
    }

    protected function getHoursSummary(DateTime $referenceDate = NULL): array
    {
        $taskDao = DAOFactory::getTaskDAO();
        $journeyHistoryDao = DAOFactory::getJourneyHistoryDAO();
        $userDao = DAOFactory::getUserDAO();

        // If no User was specified, then we retrieve all
        if (is_null($this->user)) {
            $groupDAO = DAOFactory::getUserGroupDAO();
            $users = $groupDAO->getUsersByUserGroupName(ConfigurationParametersManager::getParameter("ALL_USERS_GROUP"));
        } else {

            // The User can be identified by either the id or the login
            if (is_null($this->user->getLogin())) {
                if (!is_null($this->user->getId()))
                    $this->user = $userDao->getById($this->user->getId());
            } else
            if (is_null($this->user->getId()))
                $this->user = $userDao->getByUserLogin($this->user->getLogin());

            $users[] = $this->user;
        }

        $reportInit = $this->init;
        $reportEnd = $this->end;

        // We compute the holiday hours for each User
        foreach ((array)$users as $userVO) {
            // We only compute it for workers, so they must be in a group
            if (!is_null($userVO->getGroups())) {
                // We retrieve the user's journey data until the end of the year,
                // to include the holidays produced by journey periods that might
                // start later than the end date in the report but are still part
                // of the year in course (see #462).
                $endYearDate = new DateTime($reportEnd->format('Y') . '-12-31');
                $journeyHistory = $journeyHistoryDao->getByIntervals(
                    $reportInit,
                    $endYearDate,
                    $userVO->getId()
                );

                // He/she starts with no worked hours
                $workHours = 0;

                foreach ((array) $journeyHistory as $journeyRow) {
                    // First of all, we clip the interval with the journey
                    $initJourney = $journeyRow->getInitDate();
                    $endJourney = $journeyRow->getEndDate();

                    if ($initJourney < $reportInit) {
                        $initYearDay = date_create($reportInit->format("Y") . "-1-1");
                        if ($initJourney < $initYearDay) {
                            $initJourney = $initYearDay;
                        }
                    }

                    if ($endJourney > $reportEnd) {
                        $endYearDay = date_create($reportEnd->format("Y") . "-12-31");
                        if ($endJourney > $endYearDay) {
                            $endJourney = $endYearDay;
                        }
                    }

                    // We get the difference in days...
                    $diffJourney = $initJourney->diff($endJourney);
                    // and with it and the journey, the worked hours (plus one day because it's a closed ending interval)
                    $workHours += ($diffJourney->days + 1) * ($journeyRow->getJourney());


                    // We must check for leap years on the interval
                    $initYear = $initJourney->format("Y");

                    // Go from the init year to the end one
                    while ($initYear <= $endJourney->format("Y")) {
                        // We check if the year is a leap one, and if february 29th is in the interval. There can be some useless checkings here
                        // (february 29th can be prior to the init only in the first year, and later than the end on the last one), but it's no
                        // much computation anyway, and the code is clear (and it's already hard to understand at first)
                        if (checkdate(02, 29, $initYear))
                            if (($initJourney < date_create($initYear . "-02-29")) && ($endJourney >= date_create($initYear . "-02-29")))
                                $workHours -= $journeyRow->getJourney();        // It's a leap year, so we subtract one journey for february 29th

                        $initYear++;
                    }
                }

                // We strictly get the holidays spent between the dates specified in
                // the report, not until the end of the year (see #352).
                $vacations = $taskDao->getVacations($userVO, $reportInit, $reportEnd);
                $vacations = $vacations["add_hours"] ?? 0;

                // Yearly holiday hours is the standard for an 8-hour journey over a year, so the result is proportional
                $holidayHours = ($workHours / (365 * 8)) * ConfigurationParametersManager::getParameter('YEARLY_HOLIDAY_HOURS');
                $userAvailableHours[$userVO->getLogin()] = $holidayHours;

                $referenceDate = $referenceDate ?? new DateTime();
                $userUsedHours[$userVO->getLogin()] = $taskDao->getVacations($userVO, $reportInit, $referenceDate)["add_hours"] ?? 0;
                $userScheduledHours[$userVO->getLogin()] = $vacations - $userUsedHours[$userVO->getLogin()];

                // The difference is the number of pending holiday hours
                $userPendingHolidayHours[$userVO->getLogin()] = $holidayHours - $vacations;
            }
        }

        return [
            'pendingHours' => $userPendingHolidayHours,
            'scheduledHours' => $userScheduledHours,
            'usedHours' => $userUsedHours,
            'availableHours' => $userAvailableHours
        ];
    }
}
