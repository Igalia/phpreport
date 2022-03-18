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


class GetScheduledHolidaysAction extends Action
{

    private UserVO $user;
    private DateTime $init;
    private DateTime $end;

    public function __construct(DateTime $init, DateTime $end, UserVO $user = NULL)
    {
        $this->init = $init;
        $this->end = $end;
        $this->user = $user;
        $this->preActionParameter = "GET_SCHEDULED_HOLIDAYS_PREACTION";
        $this->postActionParameter = "GET_SCHEDULED_HOLIDAYS_POSTACTION";
    }

    protected function doExecute(): array
    {

        $taskDao = DAOFactory::getTaskDAO();
        $userDao = DAOFactory::getUserDAO();

        if (is_null($this->user)) {
            return [];
        }

        // The User can be identified by either the id or the login
        if (is_null($this->user->getLogin())) {
            if (!is_null($this->user->getId()))
                $this->user = $userDao->getById($this->user->getId());
        } else
            if (is_null($this->user->getId()))
            $this->user = $userDao->getByUserLogin($this->user->getLogin());
        $reportInit = $this->init;
        $reportEnd = $this->end;

        $vacations = $taskDao->getVacationsDates($this->user, $reportInit, $reportEnd);

        return $vacations;
    }
}
